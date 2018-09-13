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

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\DataObject\Service;

final class ObjectHelperExtensions extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new \Twig_Test('object', function ($object) {
                return is_object($object) && $object instanceof Concrete;
            }),
            new \Twig_Test('object_class', function ($object, $className) {
                $className = ucfirst($className);
                $className = 'Pimcore\\Model\\DataObject\\' . $className;

                return class_exists($className) && $object instanceof $className;
            }),
            new \Twig_Test('object_gallery', function ($object, $className) {
                return $object instanceof ImageGallery;
            }),
            new \Twig_Test('object_hotspot_image', function ($object, $className) {
                return $object instanceof Hotspotimage;
            })
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('object_select_options', function($object, $field) {
                return Service::getOptionsForSelectField($object, $field);
            })
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('object_gallery_images', function(ImageGallery $gallery = null) {
                if (null === $gallery) {
                    return [];
                }

                return array_map(function(Hotspotimage $item) {
                    return $item->getImage();
                }, $gallery->getItems());
            })
        ];
    }
}
