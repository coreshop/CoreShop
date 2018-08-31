//MAGNIFIC POPUP
$(document).ready(function() {
    /*$('.images-block').magnificPopup({
        delegate: 'a', 
        type: 'image',
        gallery: {
            enabled: true
        }
    });

    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }*/
});

(function($) {

    "use strict";

    // Product Owl Carousel
    $("#owl-product").owlCarousel({
        autoPlay: false, //Set AutoPlay to 3 seconds
        items : 3,
        stopOnHover : true,
        navigation : true, // Show next and prev buttons
        pagination : false,
        navigationText : ["<span class='glyphicon glyphicon-chevron-left'></span>","<span class='glyphicon glyphicon-chevron-right'></span>"]
    });
  
    // TABS
    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    }); 
    
})(jQuery);