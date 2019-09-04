$(document).ready(function () {
    shop.init();
});

(function (shop, $) {

    shop.init = function () {
        shop.initChangeAddress();
        shop.initCartShipmentCalculator();
        shop.initQuantityValidator();

        $('#paymentProvider').handlePrototypes({
            'prototypePrefix': 'paymentProvider',
            'containerSelector': '.paymentSettings',
            'selectorAttr': 'data-factory'
        });
    };

    shop.initQuantityValidator = function () {
        $.coreshopQuantitySelector({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',
        });
    };

    shop.initCartShipmentCalculator = function () {

        $(document).on('submit', 'form[name="coreshop_shipping_calculator"]', function (ev) {
            ev.preventDefault();
            var $form = $(this);
            $form.addClass('loading');
            $form.find('button[type="submit"]').attr('disabled', 'disabled');
            $form.closest('.cart-shipment-calculation-box').find('.cart-shipment-available-carriers').css('opacity', .2);
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    $form.removeClass('loading');
                    $form.closest('.cart-shipment-calculation-box').replaceWith($(res));
                }
            });
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
            $shippingAddAddressButton = $shippingPanel.parent().find('.card-footer'),
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

        if ($useIasS.is(':not(:checked)') && $shippingAddAddressButton) {
            $shippingAddAddressButton.removeClass('d-none');
        }

        $useIasS.on('change', function () {
            if ($(this).is(':checked')) {
                $shippingField.slideUp();
                var address = $('select[name=invoiceAddress] option:selected').data('address');
                var value = $('select[name=invoiceAddress] :selected').val();

                if (address) {
                    $shippingAddress.val(value).trigger('change');
                }
                if ($shippingAddAddressButton) {
                    $shippingAddAddressButton.addClass('d-none');
                }
            } else {
                $shippingField.slideDown();
                if ($shippingAddAddressButton) {
                    $shippingAddAddressButton.removeClass('d-none');
                }
            }
        });
    };

}(window.shop = window.shop || {}, jQuery));