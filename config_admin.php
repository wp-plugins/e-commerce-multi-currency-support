<?
/**
 * Created by JetBrains WebStorm.
 * User: misha
 * Date: 3/14/12
 * To change this template use File | Settings | File Templates.
 */

 $data = get_option('ecom_currency_convert');
 if (isset($_POST['currency_source']))
 {
    foreach ($_POST as $opt=>$val){
        if (preg_match('/currency_.*/',$opt)) $data[$opt] = attribute_escape($val);
    }
    update_option('ecom_currency_convert', $data);
 }
?>
 <div class="wrap">
     <div id="icon-options-general" class="icon32">
         <br/>
     </div>
     <div style="width:400px;float:left;">
     <form method="post" >
     <h2>e-Commerce currency convertion plugin</h2>

 	<!--<h3>Basic options</h3>-->
 	<?include "config.php";?>
         <h3>Advanced options</h3>
         <label for="currency_source">Select site to get information:</label>
                         <select name="currency_source">
                            <?
                                $sources = array("wpsc"=>"Build-in WPSC","wpsc_local"=>"reworked WPSC", "google"=>"Google");
                                foreach ($sources as $ind=>$val)
                                {
                                    print '<option value="'.$ind.'"';
                                    if ($data['currency_source']==$ind) print ' selected=selected';
                                    print '>'.$val.'</option>';
                                }
                            ?>
                         </select>
        <p class="submit">
                <input type="submit" name="Submit" class="button-primary" value="Save Changes" id="submitCalendarAdd"/>
                </p>
         </form>
     </div>
     <div style="width: 300px; float:left;">
        Please support further development of e-Commerce currency changing plugin.<br />
         <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
         <input type="hidden" name="cmd" value="_donations">
         <input type="hidden" name="business" value="beshkin@gmail.com">
         <input type="hidden" name="lc" value="EE">
         <input type="hidden" name="item_name" value="e-Commerce curencies">
         <input type="hidden" name="currency_code" value="EUR">
         <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
         <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
         <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
         </form>
    </div>
 </div>


