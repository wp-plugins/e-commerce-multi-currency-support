// This is the wp-e-commerce front end javascript for the e-commerce-currency-support plugin

var $j = jQuery.noConflict();

$j(function () {
    $j("#wpsc-mcs-widget-form select").change(function(){
        if (!$j("#wpsc-mcs-widget-form input[type=submit]").length)
            $j("#wpsc-mcs-widget-form").submit();

    });

});

