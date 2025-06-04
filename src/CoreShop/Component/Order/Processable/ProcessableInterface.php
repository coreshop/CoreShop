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

namespace CoreShop\Component\Order\Processable;

use CoreShop\Component\Order\Model\OrderInterface;

interface ProcessableInterface
{
    public function getProcessableItems(OrderInterface $order): array;

    public function getProcessedItems(OrderInterface $order): array;

    public function isFullyProcessed(OrderInterface $order): bool;

    public function isProcessable(OrderInterface $order): bool;
}
