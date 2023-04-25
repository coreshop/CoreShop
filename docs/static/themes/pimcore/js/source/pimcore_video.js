var pimcore_video;

var PimcoreVideo = (function () {
    var domSelector, $elements;
    var youtubeEmbedUrl = 'https://www.youtube.com/embed/';

    function PimcoreVideo() {
    }

    /**
     * @param selector
     * @returns {PimcoreVideo}
     */
    PimcoreVideo.prototype.setClass = function (selector) {
        domSelector = selector;

        return this;
    };

    PimcoreVideo.prototype.addElements = function ($elements) {
        return this;
    };

    /**
     * @returns {PimcoreVideo}
     */
    PimcoreVideo.prototype.init = function () {
        this.getElements().each(function () {
            var $oldElement = jQuery(this);
            $oldElement.hide();
            var $videoElement = jQuery("<iframe>");
            $videoElement.attr('src', youtubeEmbedUrl + $oldElement.data('youtubeid'))
                .attr('width', '100%')
                .attr('height', 315)
                .attr('allowfullscreen', '')
                .attr('frameborder', 0);

            $oldElement.replaceWith($videoElement);
        });

        jQuery(this).trigger("initialized", [this]);

        return this;
    };

    /**
     *
     * @returns {jQuery|null}
     */
    PimcoreVideo.prototype.getElements = function () {
        return $elements instanceof jQuery
            ? $elements
            : (undefined !== domSelector ? jQuery(domSelector) : null);

    };

    return PimcoreVideo;
}());

$(document).ready(function () {
    pimcore_video = (new PimcoreVideo()).setClass('.pimcore_videolink');
    pimcore_video.init();
});
