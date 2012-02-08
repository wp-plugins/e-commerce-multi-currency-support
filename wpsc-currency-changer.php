<?php
/*
Plugin Name: e-Commerce Multi Currency Support
Plugin URI: http://misha.beshkin.lv
Description: A plugin that provides a currency converter tool integrated into the WordPress Shopping Cart. This is trunk from wp-e-commerce-multi-currency-magic plugin.
Version: 0.4
Author: Misha Beshkin
Author URI: http://misha.beshkin.lv
*/

define('WPSC_CURRENCY_FOLDER', dirname(plugin_basename(__FILE__)));
define('WPSC_CURRENCY_URL', WP_CONTENT_URL.'/plugins/'.WPSC_CURRENCY_FOLDER);
//Include Currency Converter Class
if(defined(WPSC_FILE_PATH)){
require_once(WPSC_FILE_PATH."/wpsc-includes/currency_converter.inc.php");
}
//Include widget for sidebar
include_once('widgets/currency_chooser_widget.php');

/**
 * Description sets up the converter data, does initial conversion for for 1.00 of base currency
 * @access public
 *
 * @return none
 */
function load_wpsc_converter(){
	global $wpsc_cart, $wpdb;
	$wpsc_cart->use_currency_converter = true;

// Get currency settings
		//$currency_type = get_option( 'currency_type' );
	$currency_code = $wpdb->get_results("SELECT `code` FROM `".WPSC_TABLE_CURRENCY_LIST."` WHERE `id`='".get_option('currency_type')."' LIMIT 1",ARRAY_A);

    $local_currency_code = $currency_code[0]['code'];
	$_SESSION['wpsc_base_currency_code'] = $local_currency_code;
	if(!isset($_POST['reset'])){
		$foreign_currency_code = $wpdb->get_var("SELECT `code` FROM `".WPSC_TABLE_CURRENCY_LIST."` WHERE `id`='".$_POST['currency_option']."' LIMIT 1");
		$_SESSION['wpsc_currency_code'] =$_POST['currency_option'];
		$wpsc_cart->selected_currency_code = $foreign_currency_code;
	}else{
		$_SESSION['wpsc_currency_code'] =get_option('currency_type');
		$wpsc_cart->selected_currency_code = $local_currency_code;
		$foreign_currency_code = $local_currency_code;
	}
	$curr=new CURRENCYCONVERTER();
	//if($foreign_currency_code != '' || $foreign_currency_code != $local_currency_code){
		$wpsc_cart->currency_conversion = $curr->convert(1,$local_currency_code,$foreign_currency_code);
      //  $_SESSION['wpsc_currency_conversion'] = $wpsc_cart->currency_conversion;
	//}
	foreach($wpsc_cart->cart_items as $item){
		$item->refresh_item();
	}
//	exit('<pre>'.print_r($wpsc_cart, true).'</pre>');
	$wpsc_cart->subtotal = null;
	$wpsc_cart->total_price = null;
	$wpsc_cart->total_tax = null;

}

/**
 * Description Converts prices for all prices, called through filters, 
 * @access public
 *
 * @param price double numeric
 * @return number calculated price
 */
function wpsc_convert_price($price){
	global $wpsc_cart;
	if($wpsc_cart->use_currency_converter){
		$price = $price * $wpsc_cart->currency_conversion;
	}
	return $price;
}

/**
 * Description Adds Currency Country Code to Prices
 * @access public
 *
 * @param string $total price + currency symbol
 * @return string country code, currency symbol and total price
 */
function wpsc_add_currency_code($total){
	global $wpsc_cart;
       if ($wpsc_cart->selected_currency_code != '')
        {
            if($wpsc_cart->use_currency_converter){

    $totalpre1 = trim(preg_replace("/([^0-9\\.])/i", "",$total));
    $totalpre = (float)$totalpre1;
    $total_converted =  number_format($totalpre * $wpsc_cart->currency_conversion, 2, '.', '');
	$total = preg_replace('/[A-Z]{3}/', $wpsc_cart->selected_currency_code, $total);
    $total = str_replace($totalpre1, $total_converted , $total);


           }
        }
	return $total;//.$totalpre.$totalpre1.$total_converted;
}

/**
 * Description Reset prices to the default, and calculates new total cart price...
 * @access public
 *
 * @param none
 * @return none
 */
function wpsc_reset_prices(){
	global $wpsc_cart;
	//unset($_SESSION['wpsc_currency_code']);
	$wpsc_cart->use_currency_converter = false;
	$wpsc_cart->total_price = null;
	$wpsc_cart->subtotal = null;
	foreach((array)$wpsc_cart->cart_items as $item){
		$item->refresh_item();
	}
	
}

/**
 * Description
 * @access 
 *
 * @param 
 * @param 
 * @return 
 */
function wpsc_save_currency_info($cart_id,$product_id){
	global $wpsc_cart;
//	exit('<pre>'.print_r($product_id, true).'</pre><pre>'.print_r($cart_id, true).'</pre><br /><pre>'.print_r($wpsc_cart, true).'</pre>'.print_r(func_get_args(),true));
	$meta_key = 'wpsc_currency_conversion_rate';
	$meta_value = array( $wpsc_cart->selected_currency_code => $wpsc_cart->currency_conversion);
	wpsc_update_cartmeta( $cart_id, $meta_key, $meta_value );
	
}

/**
 * Description show currency price shows converted price viewed by user when proceeding through checkout
 * @access public
 *
 * @param purchaselog id
 * @return none
 */
function wpsc_show_currency_price($purchaselog_id){
	global $wpdb, $purchlogitem;
	$sql = "SELECT `id` FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='".$purchaselog_id."'";
	$ids = $wpdb->get_col($sql);
	foreach($ids as $id){
		$conversion_rate = wpsc_get_cartmeta($id,'wpsc_currency_conversion_rate' );
		if(is_numeric($conversion_rate)){
			break;
		}
	}
	if($conversion_rate != 0){
		$conversion_rate = maybe_unserialize($conversion_rate);
		foreach((array)$conversion_rate as $key => $value){
			echo '<br />'.$key.' '.nzshpcrt_currency_display($purchlogitem->extrainfo->totalprice*$value, 1, true);
			break;
		}
	}
	
}

if($_REQUEST['wpsc_admin_action'] == 'change_currency_country'){
		add_action('wp_head','load_wpsc_converter');
}

function wpsc_cart_price_wrap($price) {
   global $wpsc_cart;
    $args = array(
			'display_as_html' => false
		);
          return wpsc_currency_display($price, $args);
}

function wpsc_display_fancy_currency_notification(){
	global $wpsc_cart;
//	exit('<pre>'.print_r($wpsc_cart, true).'</pre>');
	if($wpsc_cart->selected_currency_code != $_SESSION['wpsc_base_currency_code']){
		$output .="<div id='wpsc_currency_notification'>";
	    $output .= "<p>".__('By clicking Make Purchase you will be redirected to the gateway, and the cart prices will be converted to the shops local currency','wpsc')." ".$_SESSION['wpsc_base_currency_code'].'</p>';
		$output .="</div>";
		echo $output;
	}
}
function wpsc_add_currency_js_css(){
	wp_enqueue_script('wpsc-multi-currency-support-js',WPSC_CURRENCY_URL.'/js-css/currency.js', array('jquery'), 'Wp-Currency-Support');
	wp_enqueue_style( 'wpsc-multi-currency-support-css', WPSC_CURRENCY_URL.'/js-css/currency.css', false, '0.0', 'all');
    load_plugin_textdomain( 'currency-changer', false, dirname( plugin_basename( __FILE__ ) ) . '/localization/' );
}
add_action('init','wpsc_add_currency_js_css', 11);
add_action('wpsc_bottom_of_shopping_cart','wpsc_display_fancy_currency_notification');
add_action('wpsc_additional_sales_amount_info','wpsc_show_currency_price',10,1);
add_action('wpsc_before_submit_checkout','wpsc_reset_prices');
add_action('wpsc_save_cart_item','wpsc_save_currency_info', 10, 2);
//add_filter('wpsc_convert_total_shipping','wpsc_convert_price');
//add_filter('wpsc_do_convert_price','wpsc_convert_price');
add_filter('wpsc_currency_display', 'wpsc_add_currency_code');
?>
