<?php
/**
 * Currency Converter widget class
 *
 * @since 3.7.1
 */
class WPSC_Widget_Currency_Converter extends WP_Widget {

	function WPSC_Widget_Currency_Converter() {

		$widget_ops = array('classname' => 'widget_wpsc_currency_chooser', 'description' => __('Product Currency Chooser Widget', 'wpscmcs'));
		$this->WP_Widget('wpsc_currency', __('Currency Chooser','wpscmcs'), $widget_ops);
    }


	function widget( $args, $instance ) {
	  global $wpdb, $wpsc_theme_path,$wpsc_cart;
		extract( $args );
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Currency Chooser' ) : $instance['title']);
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		//Display Currency Info:
		$sql ="SELECT * FROM `".WPSC_TABLE_CURRENCY_LIST."` WHERE `visible`='1' ORDER BY `country` ASC";
		//echo $_SESSION['wpsc_base_currency_isocode'];
		$countries = $wpdb->get_results($sql, ARRAY_A);
        //print_r($countries);
		$output .= '<form method="post" action="" id="wpsc-mcs-widget-form">';
		$output .='<select name="currency_option" style="width:200px;">';
        if (!isset($_SESSION['wpsc_base_currency_code']))
        {
            $currency_code = $wpdb->get_results("SELECT `code`,`isocode` FROM `".WPSC_TABLE_CURRENCY_LIST."` WHERE `id`='".get_option('currency_type')."' LIMIT 1",ARRAY_A);
            $local_currency_code = $currency_code[0]['code'];
            $local_currency_isocode = $currency_code[0]['isocode'];
        }else{
            $local_currency_code=$_SESSION['wpsc_base_currency_code'];
        }
        $only_code_ar[] = "";
        	foreach($countries as $country){
                $country_code = '';
                if ($instance['show_code'] == 1)
                {
                    if ($instance['show_code_readable'] == 1)
                        $country_code =" (".$country['currency'].")";
                    else
                        $country_code =" (".$country['code'].")";

                    if ($instance['show_code_symbol']== 1)
                        if ($country['symbol_html']!='')
                            $country_code =" (".$country['symbol_html'].")";
                }
                $selected_code = '';
				if($_SESSION['wpsc_base_currency_isocode'] == $country['isocode']){
                    $selected_code = "selected='selected'";

				}else {
                    if ( !isset($_SESSION['wpsc_base_currency_isocode']) && $local_currency_isocode == $country['isocode'])
                        $selected_code = "selected='selected'";
                }
                $output_value = $country['country'].$country_code;
                if ($instance['hide_country_name'] == 1)
                {
                    $skip = false;
                    $output_value = preg_replace("/[()]/","",$country_code);
                    if (in_array($output_value, $only_code_ar))
                        $skip = true;
                    $only_code_ar[] = $output_value;
                }
                if (!$skip)
				    $output .="<option ".$selected_code." value=".$country['id'].">".$output_value."</option>";

			}
		$output .="</select><br />";
		$output .='<input type="hidden" value="change_currency_country" class="button-primary" name="wpsc_admin_action" />';

        if($instance['show_reset'] == 1){
			$output .='<input type="submit" value="'.__('Reset Price to ','wpscmcs').$_SESSION['wpsc_base_currency_code'].'"  name="reset" />';
		}
        if($instance['show_submit'] == 1){
		    $output .='<input type="submit" value="'.__('Convert','wpscmcs').'" class="button-primary" name="submit" />';
        }
		$output .='</form>';
		if($instance['show_conversion'] == 1){
			if($wpsc_cart->currency_conversion != 0 && $wpsc_cart->use_currency_converter){		
				$output .='<p><strong>'.__("Base Currency",'wpscmcs').':</strong> '.$_SESSION['wpsc_base_currency_code'].'</p>';
				if($wpsc_cart->selected_currency_code != ''){
					$output .='<p><strong>'.__("Current Currency",'wpscmcs').':</strong> '.$wpsc_cart->selected_currency_code.'</p>';
				}
				$output .='<p>1 '.$_SESSION['wpsc_base_currency_code'].' = '.$wpsc_cart->currency_conversion.' '.$wpsc_cart->selected_currency_code.'</p><br />';
			}elseif($wpsc_cart->currency_conversion == 0 && $_SESSION['wpsc_currency_code'] != '' ){
				
			}

		}
        //if (!isset($_SESSION['wpsc_base_currency_code'])) $output='';
        //print_r($wpsc_cart);
		echo $output.$after_widget;
	}

	function update( $new_instance, $old_instance ) {
        $data = get_option('ecom_currency_convert');
		$instance = $old_instance;
	//	exit('<pre>'.print_r($new_instance,true).'</pre>');
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_conversion'] = $new_instance['show_conversion'];
		$instance['show_reset'] = $new_instance['show_reset'];
		$instance['show_submit'] = $new_instance['show_submit'];
		$instance['hide_country_name'] = $new_instance['hide_country_name'];
		$instance['show_code'] = $new_instance['show_code'];
		$instance['show_code_readable'] = $new_instance['show_code_readable'];
		$instance['show_code_symbol'] = $new_instance['show_code_symbol'];
        update_option('ecom_currency_convert', array_merge($data,$instance));
		return $instance;
	}

	function form( $instance ) {
	  global $wpdb;
       	    $title = esc_attr($instance['title']);
       	    $show_conversion =$instance['show_conversion'];
       	    $show_reset =$instance['show_reset'];
       	    $show_submit =$instance['show_submit'];
       	    $show_code =$instance['show_code'];
               if ($show_code == 1) {
                   $show_code_check = 'checked="checked"';
       	    }else{
          	    	$show_code_check = '';
       	    }
            $hide_country_name =$instance['hide_country_name'];
               if ($hide_country_name == 1) {
                   $hide_country_name_check = 'checked="checked"';
       	    }else{
          	    	$hide_country_name_check = '';
       	    }
            $show_code_readable =$instance['show_code_readable'];
               if ($show_code_readable == 1) {
                   $show_code_readable_check = 'checked="checked"';
       	    }else{
          	    	$show_code_readable_check = '';
       	    }
            $show_code_symbol =$instance['show_code_symbol'];
               if ($show_code_symbol == 1) {
                   $show_code_symbol_check = 'checked="checked"';
       	    }else{
          	    	$show_code_symbol_check = '';
       	    }

       	    if($show_conversion == 1){
       	    	$checked = 'checked="checked"';
       	    }else{
          	    	$checked = '';
       	    }
       	    if($show_reset == 1){
       	    	$show_reset_check = 'checked="checked"';
       	    }else{
          	    	$show_reset_check = '';
       	    }
               if($show_submit == 1){
       	    	$show_submit_check = 'checked="checked"';
       	    }else{
          	    	$show_submit_check = '';
       	    }
               ?>
        <a href="options-general.php?page=e-commerce-multi-currency-support/config_admin.php">Advanced settings</a>
               <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                   <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
               <p><label for="<?php echo $this->get_field_id('show_conversion'); ?>"><?php _e('Show Conversion Rate:'); ?>
                   <input  id="<?php echo $this->get_field_id('show_conversion'); ?>" name="<?php echo $this->get_field_name('show_conversion'); ?>" type="checkbox" value="1" <?php echo $checked; ?> /></label></p>
               <p><label for="<?php echo $this->get_field_id('hide_country_name'); ?>"><?php _e('Hide country name:'); ?>
                   <input  id="<?php echo $this->get_field_id('hide_country_name'); ?>" name="<?php echo $this->get_field_name('hide_country_name'); ?>" type="checkbox" value="1" <?php echo $hide_country_name_check; ?> /></label></p>
               <p><label for="<?php echo $this->get_field_id('show_code'); ?>"><?php _e('Show currency code:'); ?>
                   <input  id="<?php echo $this->get_field_id('show_code'); ?>" name="<?php echo $this->get_field_name('show_code'); ?>" type="checkbox" value="1" <?php echo $show_code_check; ?> /></label></p>
               <p style="margin-left: 20px;"><label for="<?php echo $this->get_field_id('show_code_readable'); ?>">   <?php _e('Readable format:'); ?>
                   <input disabled="disabled" id="<?php echo $this->get_field_id('show_code_readable'); ?>" name="<?php echo $this->get_field_name('show_code_readable'); ?>" type="checkbox" value="1" <?php echo $show_code_readable_check; ?> /></label></p>
               <p style="margin-left: 20px;"><label for="<?php echo $this->get_field_id('show_code_symbol'); ?>">   <?php _e('Symbol (if possible):'); ?>
                   <input disabled="disabled" id="<?php echo $this->get_field_id('show_code_symbol'); ?>" name="<?php echo $this->get_field_name('show_code_symbol'); ?>" type="checkbox" value="1" <?php echo $show_code_symbol_check; ?> /></label></p>
               <p><label for="<?php echo $this->get_field_id('show_reset'); ?>"><?php _e('Show Reset Button:'); ?>
                   <input  id="<?php echo $this->get_field_id('show_reset'); ?>" name="<?php echo $this->get_field_name('show_reset'); ?>" type="checkbox" value="1" <?php echo $show_reset_check; ?> /></label></p>
               <p><label for="<?php echo $this->get_field_id('show_submit'); ?>"><?php _e('Show Submit Button:'); ?>
                   <input  id="<?php echo $this->get_field_id('show_submit'); ?>" name="<?php echo $this->get_field_name('show_submit'); ?>" type="checkbox" value="1" <?php echo $show_submit_check; ?> /></label></p>
<script type="text/javascript">
jQuery(function() {
    if (jQuery("#<?php echo $this->get_field_id('show_code'); ?>").is(':checked'))
    {
        jQuery("#<?php echo $this->get_field_id('show_code_readable'); ?>").removeAttr("disabled");
        jQuery("#<?php echo $this->get_field_id('show_code_symbol'); ?>").removeAttr("disabled");
    }
    jQuery("#<?php echo $this->get_field_id('show_code'); ?>").click(function() {
        if (jQuery("#<?php echo $this->get_field_id('show_code'); ?>").is(':checked'))
        {
            jQuery("#<?php echo $this->get_field_id('show_code_readable'); ?>").removeAttr("disabled");
            jQuery("#<?php echo $this->get_field_id('show_code_symbol'); ?>").removeAttr("disabled");
        }else{
            jQuery("#<?php echo $this->get_field_id('show_code_readable'); ?>").attr("disabled", true);
            jQuery("#<?php echo $this->get_field_id('show_code_symbol'); ?>").attr("disabled", true);
        }
    });
    jQuery("#<?php echo $this->get_field_id('hide_country_name'); ?>").click(function() {
        if (jQuery("#<?php echo $this->get_field_id('hide_country_name'); ?>").is(':checked'))
        {
            jQuery("#<?php echo $this->get_field_id('show_code'); ?>").attr("checked", true);

        }
    });
});
</script>
<?php
	
	}
    function options_page () {
		add_options_page('e-Commerce currency converter advanced options', 'e-Commerce currency converter', 8,"e-commerce-multi-currency-support/config_admin.php" );
	}
}


add_action('widgets_init', create_function('', 'return register_widget("WPSC_Widget_Currency_Converter");'));
add_action('admin_menu', array('WPSC_Widget_Currency_Converter', 'options_page'));
?>