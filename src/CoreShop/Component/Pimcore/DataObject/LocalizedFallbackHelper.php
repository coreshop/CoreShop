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

use Pimcore\Model\DataObject\Localizedfield;

class LocalizedFallbackHelper
{
    public static function useFallback(\Closure $function, bool $use = false): mixed
    {
        $backup = Localizedfield::getGetFallbackValues();

        Localizedfield::setGetFallbackValues($use);

        $result = $function();

        Localizedfield::setGetFallbackValues($backup);

        return $result;
    }
}
