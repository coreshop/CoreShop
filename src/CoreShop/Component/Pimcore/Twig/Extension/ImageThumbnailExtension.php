<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\Asset\Image;

final class ImageThumbnailExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('image_thumbnail', [$this, 'getImageThumbnail'], ['is_safe' => ['html']]),
            new \Twig_Filter('image_thumbnail_html', [$this, 'getImageThumbnailHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('image_thumbnail', [$this, 'getImageThumbnail'], ['is_safe' => ['html']]),
            new \Twig_Function('image_thumbnail_html', [$this, 'getImageThumbnailHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param Image $image
     * @param       $thumbnail
     * @param bool  $deferred
     * @return Image\Thumbnail
     */
    public function getImageThumbnail(Image $image, $thumbnail, $deferred = true)
    {
        return $image->getThumbnail($thumbnail, $deferred);
    }

    /**
     * @param Image $image
     * @param       $thumbnail
     * @param array $options
     * @param array $removeAttributes
     * @param bool  $deferred
     * @return string
     */
    public function getImageThumbnailHtml(
        Image $image,
        $thumbnail,
        $options = [],
        $removeAttributes = [],
        $deferred = true
    ) {
        return $this->getImageThumbnail($image, $thumbnail, $deferred)->getHTML($options, $removeAttributes);
    }
}
