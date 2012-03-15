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
                                $sources = array("wpsc"=>"Build-in WPSC", "google"=>"Google");
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
 </div>


