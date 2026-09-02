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

namespace CoreShop\Bundle\IndexBundle\Order\Mysql;

use CoreShop\Component\Index\Order\OrderInterface;
use CoreShop\Component\Index\Order\SimpleOrder;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class SimpleOrderRenderer extends AbstractMysqlDynamicRenderer
{
    public function render(WorkerInterface $worker, OrderInterface $order, ?string $prefix = null): string
    {
        /**
         * @var SimpleOrder $order
         */
        Assert::isInstanceOf($order, SimpleOrder::class);

        return '' . $this->quoteFieldName($order->getKey(), $prefix) . ' ' . $order->getDirection();
    }

    public function supports(WorkerInterface $worker, OrderInterface $order): bool
    {
        return $worker instanceof MysqlWorkerInterface && $order instanceof SimpleOrder;
    }
}
