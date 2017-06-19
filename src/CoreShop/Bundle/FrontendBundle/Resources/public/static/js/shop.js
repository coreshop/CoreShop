
addToCartRunning = false;

$(document).ready(function(){
    shop.init();
});

(function(shop, $, undefined) {

    shop.init = function() {
        shop.initChangeAddress();
    };
    
    shop.initChangeAddress = function()
    {
        $('select[name=shippingAddress]').change(function() {
            var value = $(this).val();
            
            $('.panel-shipping-address').html($('#address-' + value).html());
            
            if($('[name=useShippingAsInvoice]').is(":checked"))
            {
                $('.panel-invoice-address').html($('#address-' + value).html());
                
                $('select[name=invoiceAddress]').val($(this).val());
            }
        });
        
        $('select[name=invoiceAddress]').change(function(){
            var value = $(this).val();
            value = $(this).find("[value='"+value+"']").data("value");
            
            $('.panel-invoice-address').html($('#address-' + value).html());
        });
        
        $('[name=useShippingAsInvoice]').change(function(){
            if($(this).is(":checked"))
            {
                $('.invoice-address-selector').slideUp();
                
                var value = $('select[name=invoice-address] :selected').val();
                var htmlValue = $('select[name=shipping-address]').find("[value='"+value+"']").data("value");

                $('.panel-invoice-address').html($('#address-' + htmlValue).html());
                
                $('select[name=invoice-address]').val(value);
            }
            else
            {
                $('.invoice-address-selector').slideDown();
            }
        });
    };
    
}( window.shop = window.shop || {}, jQuery ));