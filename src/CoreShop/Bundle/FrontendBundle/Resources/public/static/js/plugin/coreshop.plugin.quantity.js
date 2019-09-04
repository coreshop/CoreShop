;(function ($) {
    $.coreshopQuantitySelector = function (options) {
        initQuantityFields(options);
    };

    function initQuantityFields(options) {

        var $fields = $('input.cs-unit-input'),
            $precisionPresetSelector = $('select.cs-unit-selector'),
            touchSpinOptions = $.extend( options, {} );

        // listen to unit definition selector
        $precisionPresetSelector.on('change', function () {

            if (!$(this).data('cs-unit-identifier')) {
                return;
            }

            var $selectedOption = $(this).find(':selected'),
                quantityIdentifier = $(this).data('cs-unit-identifier'),
                $quantityInput = $('input[data-cs-unit-identifier="' + quantityIdentifier + '"]'),
                precision = $selectedOption.data('cs-unit-precision') ? $selectedOption.data('cs-unit-precision') : 0,
                strPrecision = '0.' + (Array(precision).join('0')) + '1';

            if ($quantityInput.length === 0) {
                return;
            }

            $quantityInput.attr('data-cs-unit-precision', precision);
            $quantityInput.trigger('touchspin.updatesettings', {
                min: 0,
                decimals: precision,
                step: precision === 0 ? 1 : strPrecision
            });

        });

        // add quantity validation based on precision
        $fields.each(function () {
            var precision = isNaN($(this).attr('data-cs-unit-precision')) ? 0 : parseInt($(this).attr('data-cs-unit-precision')),
                strPrecision = '0.' + (Array(precision).join('0')) + '1';

            console.log($(this));

            $(this).TouchSpin($.extend({
                verticalbuttons: true,
                callback_before_calculation: function(v) {
                    console.log(v);
                    return 99;
                },
                min: 0,
                max: 1000000000,
                decimals: precision,
                step: precision === 0 ? 1 : strPrecision,
            }, touchSpinOptions));
        });
    }
})(jQuery);