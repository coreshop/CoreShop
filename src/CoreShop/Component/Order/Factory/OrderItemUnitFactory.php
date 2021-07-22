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

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderItemUnitInterface;
use CoreShop\Component\Resource\Exception\UnsupportedMethodException;

class OrderItemUnitFactory implements OrderItemUnitFactoryInterface
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function createNew(): OrderItemUnitInterface
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function createForItem(OrderItemInterface $orderItem): OrderItemUnitInterface
    {
        /**
         * @var OrderItemUnitInterface $orderItemUnit
         */
        $orderItemUnit = new $this->className($orderItem);
        $orderItemUnit->setKey(uniqid());
        $orderItemUnit->setParent($orderItem);
        $orderItemUnit->setPublished(true);

        $orderItem->addUnit($orderItemUnit);

        return $orderItemUnit;
    }
}
