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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\StorageList\Context\CompositeStorageListContext;

final class CompositeCartContext extends CompositeStorageListContext implements CartContextInterface
{
    public function getCart(): OrderInterface
    {
        return $this->getStorageList();
    }

    public function getStorageList(): OrderInterface
    {
        $order = parent::getStorageList();

        if (!$order instanceof OrderInterface) {
            throw new CartNotFoundException();
        }

        return $order;
    }
}
