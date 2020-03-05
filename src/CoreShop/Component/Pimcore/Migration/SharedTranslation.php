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

namespace CoreShop\Component\Pimcore\Migration;

use Pimcore\Model\Translation\Website;

class SharedTranslation
{
    public static function add(string $key, string $language, string $value)
    {
        $key = Website::getByKey($key, true);
        $key->addTranslation($language, $value);
        $key->save();
    }

    public static function cleanup(): void
    {
        $list = new Website\Listing();

        if (method_exists($list, 'cleanup')) {
            $list->cleanup();
        }
    }
}
