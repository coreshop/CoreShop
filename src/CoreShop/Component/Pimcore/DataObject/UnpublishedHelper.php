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

use Pimcore\Model\DataObject\Concrete;

class UnpublishedHelper
{
    /**
     * This function enables usage of unpublished/published in Pimcore and resets the state hideUnpublished
     * after your functions is finished.
     *
     *
     * @return mixed
     */
    public static function hideUnpublished(\Closure $function, bool $hide = false)
    {
        $backup = Concrete::getHideUnpublished();

        Concrete::setHideUnpublished($hide);

        $result = $function();

        Concrete::setHideUnpublished($backup);

        return $result;
    }
}
