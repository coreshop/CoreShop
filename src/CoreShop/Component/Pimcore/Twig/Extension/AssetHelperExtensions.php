<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\Asset;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class AssetHelperExtensions extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('asset', static function ($object) {
                return is_object($object) && $object instanceof Asset;
            }),
            new TwigTest('asset_archive', static function ($object) {
                return is_object($object) && $object instanceof Asset\Archive;
            }),
            new TwigTest('asset_audio',static  function ($object) {
                return is_object($object) && $object instanceof Asset\Audio;
            }),
            new TwigTest('asset_document', static function ($object) {
                return is_object($object) && $object instanceof Asset\Document;
            }),
            new TwigTest('asset_folder', static function ($object) {
                return is_object($object) && $object instanceof Asset\Folder;
            }),
            new TwigTest('asset_image', static function ($object) {
                return is_object($object) && $object instanceof Asset\Image;
            }),
            new TwigTest('asset_text', static function ($object) {
                return is_object($object) && $object instanceof Asset\Text;
            }),
            new TwigTest('asset_unknown', static function ($object) {
                return is_object($object) && $object instanceof Asset\Unknown;
            }),
            new TwigTest('asset_video', static function ($object) {
                return is_object($object) && $object instanceof Asset\Video;
            }),
        ];
    }
}
