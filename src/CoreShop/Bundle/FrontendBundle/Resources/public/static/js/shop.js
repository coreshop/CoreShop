addToCartRunning = false;

$(document).ready(function () {
    shop.init();
});

(function (shop, $, undefined) {

    shop.init = function () {
        shop.initChangeAddress();
        shop.initAjaxSteps();
    };

    shop.initAjaxSteps = function () {

        var $checkoutStep = $('.checkout-step'),
            validStepChanger = ['#paymentProvider'];

        if($checkoutStep.length === 0) {
            return;
        }

        var $form = $checkoutStep.find('form');
        if($form.length === 0) {
            return;
        }

        $checkoutStep.on('change', validStepChanger.join(','), function(ev) {
            //implement change here
        });
    };

    shop.initChangeAddress = function () {
        if ($('select[name=shippingAddress]').find('option:selected').length) {
            var address = $('select[name=shippingAddress]').find('option:selected').data('address');
            if (address) {
                $('.panel-shipping-address').html(address.html);
            }
        }

        if ($('select[name=invoiceAddress]').find('option:selected').length) {
            var address = $('select[name=invoiceAddress]').find('option:selected').data('address');
            if (address) {
                $('.panel-invoice-address').html(address.html);
            }
        }

        $('select[name=shippingAddress]').change(function () {
            var address = $(this).find('option:selected').data('address');

            if (address) {
                address = address.html;

                $('.panel-shipping-address').html(address);

                if ($('[name=useShippingAsInvoice]').is(":checked")) {
                    $('.panel-invoice-address').html(address);

                    $('select[name=invoiceAddress]').val($(this).val());
                }
            }
            else {
                $('.panel-shipping-address').html('');

                if ($('[name=useShippingAsInvoice]').is(":checked")) {
                    $('.panel-invoice-address').html('');
                    $('select[name=invoiceAddress]').val(null);
                }
            }
        });

        $('select[name=invoiceAddress]').change(function () {
            var address = $(this).find('option:selected').data('address');

            if (address) {
                address = address.html;

                $('.panel-invoice-address').html(address);
            }
            else {
                $('.panel-invoice-address').html('');
            }
        });

        $('[name=useShippingAsInvoice]').change(function () {
            if ($(this).is(":checked")) {
                $('.invoice-address-selector').slideUp();
                var address = $('select[name=shippingAddress] option:selected').data('address');
                var value = $('select[name=shippingAddress] :selected').val();

                if (address) {
                    $('.panel-invoice-address').html(address.html);
                    $('select[name=invoiceAddress]').val(value);
                }
            }
            else {
                $('.invoice-address-selector').slideDown();
            }
        });
    };

}(window.shop = window.shop || {}, jQuery));