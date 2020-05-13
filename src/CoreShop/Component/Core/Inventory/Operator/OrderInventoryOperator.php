<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Inventory\Operator;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\OrderPaymentStates;
use Doctrine\Common\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    private $productEntityManager;

    public function __construct(ObjectManager $productEntityManager)
    {
        $this->productEntityManager = $productEntityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(OrderInterface $order): void
    {
        if (in_array(
            $order->getPaymentState(),
            [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_REFUNDED],
            true
        )) {
            $this->giveBack($order);

            return;
        }

        $this->release($order);
    }

    /**
     * {@inheritdoc}
     */
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

            $product->setOnHold($product->getOnHold() + $orderItem->getDefaultUnitQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
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
                ($product->getOnHold() - $orderItem->getDefaultUnitQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a product "%s".',
                    $product->getName()
                )
            );

            Assert::greaterThanEq(
                ($product->getOnHand() - $orderItem->getDefaultUnitQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hand quantity from the inventory of a product "%s".',
                    $product->getName()
                )
            );

            $product->setOnHold($product->getOnHold() - $orderItem->getDefaultUnitQuantity());
            $product->setOnHand($product->getOnHand() - $orderItem->getDefaultUnitQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
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
                ($product->getOnHold() - $orderItem->getDefaultUnitQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a product "%s".',
                    $product->getName()
                )
            );
            $product->setOnHold($product->getOnHold() - $orderItem->getDefaultUnitQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
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

            $product->setOnHand($product->getOnHand() + $orderItem->getDefaultUnitQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }
}
