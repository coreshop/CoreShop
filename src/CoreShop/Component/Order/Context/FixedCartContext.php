<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

    public function setCart(OrderInterface $cart): void
    {
        $this->cart = $cart;
    }
}
