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

namespace CoreShop\Component\Core\CartItem\Rule\Applier;

use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;

interface CartItemRuleApplierInterface
{
    public function applyDiscount(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        bool $withTax = false
    ): void;

    public function applySurcharge(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        bool $withTax = false
    ): void;
}
