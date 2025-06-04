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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Order\Model\OrderInterface;

final class FixedCartContext implements CartContextInterface
{
    private ?OrderInterface $cart = null;

    public function getCart(): OrderInterface
    {
        if ($this->cart instanceof OrderInterface) {
            return $this->cart;
        }

        throw new CartNotFoundException();
    }

    public function getStorageList(): OrderInterface
    {
        return $this->getCart();
    }

    public function setCart(OrderInterface $cart): void
    {
        $this->cart = $cart;
    }
}
