<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
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
