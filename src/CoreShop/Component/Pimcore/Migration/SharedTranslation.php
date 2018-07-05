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

namespace CoreShop\Component\Pimcore\Migration;

use Pimcore\Model\Translation\Website;

class SharedTranslation
{
    /**
     * Add a new Shared Translation.
     *
     * @param $key
     * @param $language
     * @param $value
     */
    public static function add($key, $language, $value)
    {
        $key = Website::getByKey($key, true);
        $key->addTranslation($language, $value);
        $key->save();
    }

    /**
     * Cleanup Translations.
     */
    public static function cleanup()
    {
        $list = new Website\Listing();
        $list->cleanup();
    }
}
