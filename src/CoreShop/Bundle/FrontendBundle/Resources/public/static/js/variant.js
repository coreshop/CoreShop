$(document).ready(function () {
    variant.init();
});

(function (variant, $) {
    variant.init = function() {
        const variants = document.querySelector('.product-info__attributes');
        if(!variants) {
            return;
        }

        $.coreshopVariantSelector(variants);

        variants.addEventListener('variant_selector.select', (e) => {
            const options = document.querySelector('.product-info .product-details .options');
            const submits = options.querySelectorAll('[type="submit"]');

            options.classList.add('disabled');

            submits.forEach((submit) => {
                submit.disabled = true;
            });
        });
    };
}(window.variant = window.variant || {}, jQuery));