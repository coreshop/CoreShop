<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Order\Mysql;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Component\Index\Order\OrderInterface;
use CoreShop\Component\Index\Order\SimpleOrder;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class SimpleOrderRenderer extends AbstractMysqlDynamicRenderer
{
    public function render(WorkerInterface $worker, OrderInterface $order, string $prefix = null): string
    {
        /**
         * @var SimpleOrder $order
         */
        Assert::isInstanceOf($order, SimpleOrder::class);

        return '' . $this->quoteFieldName($order->getKey(), $prefix) . ' ' . $order->getDirection();
    }

    public function supports(WorkerInterface $worker, OrderInterface $order): bool
    {
        return $worker instanceof MysqlWorker && $order instanceof SimpleOrder;
    }
}
