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

namespace CoreShop\Component\Order\Processor;

use CoreShop\Component\Order\Model\OrderItemInterface;

interface CartItemProcessorInterface
{
    public function processCartItem(
        OrderItemInterface $cartItem,
        int $itemPrice,
        int $itemRetailPrice,
        int $itemDiscountPrice,
        int $itemDiscount,
        array $context,
    ): void;
}
