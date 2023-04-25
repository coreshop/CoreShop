/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
/** global localStorage*///Initialize CodeBlock Visibility Settings
$(function () {
    var codeBlockView = $('.Columns__right'),
        codeBlocks = $('.s-content pre'),
        toggleCodeSection = $('.CodeToggler'),
        toggleCodeBlockBtns = toggleCodeSection.find('.CodeToggler__button'),
        toggleCodeBlockBtn = toggleCodeSection.find('.CodeToggler__button--main'),
        toggleCodeBlockBtnHide = toggleCodeSection.find('.CodeToggler__button--hide'),
        toggleCodeBlockBtnBelow = toggleCodeSection.find('.CodeToggler__button--below'),
        toggleCodeBlockBtnFloat = toggleCodeSection.find('.CodeToggler__button--float');

    // If there is no code block we hide the link
    if (!codeBlocks.size()) {
        toggleCodeSection.addClass('hidden');
        return;
    }

    function setCodeBlockStyle(codeBlockState) {
        localStorage.setItem("codeBlockState", codeBlockState);

        toggleCodeBlockBtns.removeClass("Button--active");

        switch (codeBlockState) {
            case 2: // Show code blocks inline
                toggleCodeBlockBtnFloat.addClass("Button--active");
                codeBlockView.addClass('Columns__right--float');
                codeBlocks.removeClass('hidden');
                break;
            case 1: // Show code blocks below
                toggleCodeBlockBtnBelow.addClass("Button--active");
                toggleCodeBlockBtn.html("Hide Code Blocks");
                codeBlockView.removeClass('Columns__right--float');
                codeBlocks.removeClass('hidden');
                break;
            case 0: // Hidden code blocks
            default:
                toggleCodeBlockBtnHide.addClass("Button--active");
                toggleCodeBlockBtn.html("Show Code Blocks");
                codeBlockView.removeClass('Columns__right--float');
                codeBlocks.addClass('hidden');
                break;
        }
    }

    toggleCodeBlockBtn.click(function() {
        setCodeBlockStyle(codeBlocks.hasClass('hidden') ? 1 : 0);
    });

    toggleCodeBlockBtnHide.click(function() { setCodeBlockStyle(0); });
    toggleCodeBlockBtnBelow.click(function() { setCodeBlockStyle(1); });
    toggleCodeBlockBtnFloat.click(function() { setCodeBlockStyle(2); });

    var floating = $(document.body).hasClass("with-float");
    var codeBlockState = localStorage.getItem("codeBlockState");

    if (!codeBlockState) {
        codeBlockState = floating? 2 : 1;
    } else {
        codeBlockState = parseInt(codeBlockState);
    }

    if (!floating && codeBlockState == 2) {
        codeBlockState = 1;
    }

    setCodeBlockStyle(codeBlockState);
});


$(function () {
    // Tree navigation
    $('.aj-nav').click(function (e) {
        e.preventDefault();
        $(this).parent().siblings().find('ul').slideUp();
        $(this).next().slideToggle();
    });

    // New Tree navigation
    $('ul.Nav > li.has-children > a > .Nav__arrow').click(function() {
        $(this).parent().parent().toggleClass('Nav__item--open');
        return false;
    });

    // Responsive navigation
    $('.Collapsible__trigger').click(function () {
        $('.Collapsible__content').slideToggle();
    });
});

