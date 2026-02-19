<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

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
