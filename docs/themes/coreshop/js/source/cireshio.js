$(document).ready(function() {
    'use strict';

    // open the first nav level on the landing page
    function openFirstNavLevel(container) {
        container.find('> .Nav__item.has-children').each(function() {
            $(this).addClass('Nav__item--open');
        });
    }

    // sidebar menu
    openFirstNavLevel($('.landingpage aside.Columns__left div > .Nav'));

    // content menu trees
    openFirstNavLevel($('.landingpage .Columns__landing div > .Nav'));

    // add links to headings
    $('h1, h2, h3, h4, h5, h6').each(function() {
        var heading = $(this);
        if (!heading.attr('id')) {
            return;
        }

        var link = $('<a class="headerlink">&para;</a>');
        link.attr('href', '#' + heading.attr('id'));

        heading.append(link);
    });
});
