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

namespace CoreShop\Component\Core\CartItem\Rule\Applier;

use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;

interface CartItemRuleApplierInterface
{
    public function applyDiscount(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        bool $withTax = false,
    ): void;

    public function applySurcharge(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        bool $withTax = false,
    ): void;
}
