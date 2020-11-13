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
                @trigger_error(
                    'asset 3.0.0 and will be removed in 3.1.0. Use pimcore_asset instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset;
            }),
            new TwigTest('asset_archive', static function ($object) {
                @trigger_error(
                    'asset_archive 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_archive instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Archive;
            }),
            new TwigTest('asset_audio',static  function ($object) {
                @trigger_error(
                    'asset_audio 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_audio instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Audio;
            }),
            new TwigTest('asset_document', static function ($object) {
                @trigger_error(
                    'asset_document 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_document instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Document;
            }),
            new TwigTest('asset_folder', static function ($object) {
                @trigger_error(
                    'asset_folder 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_folder instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Folder;
            }),
            new TwigTest('asset_image', static function ($object) {
                @trigger_error(
                    'asset_image 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_image instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Image;
            }),
            new TwigTest('asset_text', static function ($object) {
                @trigger_error(
                    'asset_text 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_text instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Text;
            }),
            new TwigTest('asset_unknown', static function ($object) {
                @trigger_error(
                    'asset_unknown 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_unknown instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Unknown;
            }),
            new TwigTest('asset_video', static function ($object) {
                @trigger_error(
                    'asset_video 3.0.0 and will be removed in 3.1.0. Use pimcore_asset_video instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Asset\Video;
            }),
        ];
    }
}
