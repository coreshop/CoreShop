addToCartRunning = false;

$(document).ready(function () {
    shop.init();
});

(function (shop, $, undefined) {

    shop.init = function () {
        shop.initChangeAddress();

        $('#paymentProvider').handlePrototypes({
            'prototypePrefix': 'paymentProvider',
            'containerSelector': '.paymentSettings',
            'selectorAttr': 'data-factory'
        });
    };

    shop.initChangeAddress = function () {

        var $addressStep = $('.checkout-step.step-address');

        if ($addressStep.length === 0) {
            return;
        }

        var $invoiceAddress = $addressStep.find('select[name="invoiceAddress"]'),
            $invoicePanel = $addressStep.find('.panel-invoice-address'),
            $invoiceField = $addressStep.find('.invoice-address-selector'),
            $shippingAddress = $addressStep.find('select[name="shippingAddress"]'),
            $shippingPanel = $addressStep.find('.panel-shipping-address'),
            $shippingField = $addressStep.find('.shipping-address-selector'),
            $useIasS = $addressStep.find('[name="useInvoiceAsShipping"]');

        if ($invoiceAddress.find('option:selected').length) {
            var address = $invoiceAddress.find('option:selected').data('address');
            if (address) {
                $invoicePanel.html(address.html);
            }
        }

        if ($shippingAddress.find('option:selected').length) {
            var address = $shippingAddress.find('option:selected').data('address');
            if (address) {
                $shippingPanel.html(address.html);
            }
        }

        $invoiceAddress.on('change', function () {
            var address = $(this).find('option:selected').data('address');

            if (address) {
                address = address.html;
                $invoicePanel.html(address);
                if ($useIasS.is(':checked')) {
                    $shippingAddress.val($(this).val()).trigger('change');
                }
            } else {
                $invoicePanel.html('');
                if ($useIasS.is(':checked')) {
                    $shippingPanel.html('');
                    $shippingAddress.val(null).trigger('change');
                }
            }
        });

        $shippingAddress.on('change', function () {
            var address = $(this).find('option:selected').data('address');

            if (address) {
                address = address.html;
                $shippingPanel.html(address);
            } else {
                $shippingPanel.html('');
            }
        });

        $useIasS.on('change', function () {
            if ($(this).is(':checked')) {
                $shippingField.slideUp();
                var address = $('select[name=invoiceAddress] option:selected').data('address');
                var value = $('select[name=invoiceAddress] :selected').val();

                if (address) {
                    $shippingAddress.val(value).trigger('change');
                }
            }
            else {
                $shippingField.slideDown();
            }
        });
    };

}(window.shop = window.shop || {}, jQuery));