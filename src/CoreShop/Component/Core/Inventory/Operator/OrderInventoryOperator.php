<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Inventory\Operator;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

final class OrderInventoryOperator implements OrderInventoryOperatorInterface
{
    /**
     * @var WorkflowManagerInterface
     */
    private $orderWorkflowManager;

    /**
     * @var ObjectManager
     */
    private $productEntityManager;

    /**
     * @param WorkflowManagerInterface $orderWorkflowManager
     * @param ObjectManager            $productEntityManager
     */
    public function __construct(WorkflowManagerInterface $orderWorkflowManager, ObjectManager $productEntityManager)
    {
        $this->orderWorkflowManager = $orderWorkflowManager;
        $this->productEntityManager = $productEntityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function hold(OrderInterface $order)
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

            $product->setOnHold($product->getOnHold() + $orderItem->getQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function sell(OrderInterface $order)
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
                ($product->getOnHold() - $orderItem->getQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a product "%s".',
                    $product->getName()
                )
            );

            Assert::greaterThanEq(
                ($product->getOnHand() - $orderItem->getQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hand quantity from the inventory of a product "%s".',
                    $product->getName()
                )
            );

            $product->setOnHold($product->getOnHold() - $orderItem->getQuantity());
            $product->setOnHand($product->getOnHand() - $orderItem->getQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    /**
     * @param OrderInterface $order
     */
    public function release(OrderInterface $order)
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
                ($product->getOnHold() - $orderItem->getQuantity()),
                0,
                sprintf(
                    'Not enough units to decrease on hold quantity from the inventory of a product "%s".',
                    $product->getName()
                )
            );
            $product->setOnHold($product->getOnHold() - $orderItem->getQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }

    /**
     * @param OrderInterface $order
     */
    public function giveBack(OrderInterface $order)
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

            $product->setOnHand($product->getOnHand() + $orderItem->getQuantity());
            $this->productEntityManager->persist($product);
        }

        $this->productEntityManager->flush();
    }
}
