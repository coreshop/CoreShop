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

use Pimcore\Model\Asset;

final class AssetHelperExtensions extends \Twig_Extension
{
    public function getTests()
    {
        return [
            new \Twig_Test('asset', function ($object) {
                return is_object($object) && $object instanceof Asset;
            }),
            new \Twig_Test('asset_archive', function ($object) {
                return is_object($object) && $object instanceof Asset\Archive;
            }),
            new \Twig_Test('asset_audio', function ($object) {
                return is_object($object) && $object instanceof Asset\Audio;
            }),
            new \Twig_Test('asset_document', function ($object) {
                return is_object($object) && $object instanceof Asset\Document;
            }),
            new \Twig_Test('asset_folder', function ($object) {
                return is_object($object) && $object instanceof Asset\Folder;
            }),
            new \Twig_Test('asset_image', function ($object) {
                return is_object($object) && $object instanceof Asset\Image;
            }),
            new \Twig_Test('asset_text', function ($object) {
                return is_object($object) && $object instanceof Asset\Text;
            }),
            new \Twig_Test('asset_unknown', function ($object) {
                return is_object($object) && $object instanceof Asset\Unknown;
            }),
            new \Twig_Test('asset_video', function ($object) {
                return is_object($object) && $object instanceof Asset\video;
            }),
        ];
    }
}
