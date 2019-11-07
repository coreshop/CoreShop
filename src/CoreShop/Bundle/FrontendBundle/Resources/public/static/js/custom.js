(function ($) {

    'use strict';

    $("#owl-product").owlCarousel({
        autoPlay: false,
        items: 3,
        stopOnHover: true,
        navigation: true,
        pagination: false,
        navigationText: ["<span class='glyphicon glyphicon-chevron-left'></span>", "<span class='glyphicon glyphicon-chevron-right'></span>"]
    });

    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

})(jQuery);