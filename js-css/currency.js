// This is the wp-e-commerce front end javascript for the e-commerce-currency-support plugin

var $j = jQuery.noConflict();

$j(function () {
    $j("#wpsc-mcs-widget-form select").change(function(){
        $j("#wpsc-mcs-widget-form").submit();
        console.log('test');
    });
    //$j("#checkout_total").html('test');
});

