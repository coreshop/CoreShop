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

namespace CoreShop\Component\Core\Inventory\Operator;

use CoreShop\Component\Core\Model\OrderInterface;

interface OrderInventoryOperatorInterface
{
    public function hold(OrderInterface $order): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function sell(OrderInterface $order): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function release(OrderInterface $order): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function giveBack(OrderInterface $order): void;
}
