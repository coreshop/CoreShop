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
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\OrderPaymentStates;
use Doctrine\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    public function __construct(
        private ObjectManager $productEntityManager,
    ) {
    }

    public function cancel(OrderInterface $order): void
    {
        if (in_array(
            $order->getPaymentState(),
            [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
            true,
        )) {
            $this->giveBack($order);

            return;
        }

        $this->release($order);
    }

    public function hold(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (!$product->getIsTracked()) {
                continue;
            }

            $product->setOnHold($product->getOnHold() + (int) ceil($orderItem->getDefaultUnitQuantity()));
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    public function sell(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (!$product->getIsTracked()) {
                continue;
            }

            Assert::greaterThanEq(
                ($product->getOnHold() - (int) ceil($orderItem->getDefaultUnitQuantity())),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a product "%s".',
                    $product->getName(),
                ),
            );

            Assert::greaterThanEq(
                ($product->getOnHand() - (int) ceil($orderItem->getDefaultUnitQuantity())),
                0,
                sprintf(
                    'Not enough units to decrease on hand quantity from the inventory of a product "%s".',
                    $product->getName(),
                ),
            );

            $product->setOnHold($product->getOnHold() - (int) ceil($orderItem->getDefaultUnitQuantity()));
            $product->setOnHand($product->getOnHand() - (int) ceil($orderItem->getDefaultUnitQuantity()));
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    public function release(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (!$product->getIsTracked()) {
                continue;
            }

            Assert::greaterThanEq(
                ($product->getOnHold() - (int) ceil($orderItem->getDefaultUnitQuantity())),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a product "%s".',
                    $product->getName(),
                ),
            );
            $product->setOnHold($product->getOnHold() - (int) ceil($orderItem->getDefaultUnitQuantity()));
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    public function giveBack(OrderInterface $order): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (!$product->getIsTracked()) {
                continue;
            }

            $product->setOnHand($product->getOnHand() + (int) ceil($orderItem->getDefaultUnitQuantity()));
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }
}
