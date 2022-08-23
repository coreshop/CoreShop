<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

class LocaleFallbackHelper
{
    /**
     * This function enables usage of locale fallback in Pimcore and resets the state of locale fallback automatically
     * after your functions is finished.
     */
    public static function useFallbackValues(\Closure $function, bool $useFallback = true): mixed
    {
        $backup = DataObject\Localizedfield::getGetFallbackValues();
        DataObject\Localizedfield::setGetFallbackValues($useFallback);

        $result = $function();

        DataObject\Localizedfield::setGetFallbackValues($backup);

        return $result;
    }
}
