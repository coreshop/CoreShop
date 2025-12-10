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

class InheritanceHelper
{
    /**
     * This function enables usage of inherited values in Pimcore and resets the state of inheritance automatically
     * after your functions is finished.
     */
    public static function useInheritedValues(\Closure $function, bool $inheritValues = true): mixed
    {
        $backup = DataObject\AbstractObject::getGetInheritedValues();
        DataObject\AbstractObject::setGetInheritedValues($inheritValues);

        $result = $function();

        DataObject\AbstractObject::setGetInheritedValues($backup);

        return $result;
    }
}
