<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\DataObject;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

final class ObjectHelperExtensions extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('object', function ($object) {
                return is_object($object) && $object instanceof DataObject\Concrete;
            }),
            new TwigTest('object_folder', function ($object) {
                return is_object($object) && $object instanceof DataObject\Folder;
            }),
            new TwigTest('object_class', function ($object, $className) {
                $className = ucfirst($className);
                $className = 'Pimcore\\Model\\DataObject\\' . $className;

                return class_exists($className) && $object instanceof $className;
            }),
            new TwigTest('object_gallery', function ($object) {
                return $object instanceof DataObject\Data\ImageGallery;
            }),
            new TwigTest('object_hotspot_image', function ($object) {
                return $object instanceof DataObject\Data\Hotspotimage;
            }),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('object_method', function ($object, $methodName) {
                return is_object($object) && method_exists($object, $methodName);
            }),
            new TwigFunction('object_select_options', function ($object, $field) {
                return DataObject\Service::getOptionsForSelectField($object, $field);
            }),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('object_gallery_images', function (DataObject\Data\ImageGallery $gallery = null) {
                if (null === $gallery) {
                    return [];
                }

                return array_map(function (DataObject\Data\Hotspotimage $item) {
                    return $item->getImage();
                }, $gallery->getItems());
            }),
        ];
    }
}
