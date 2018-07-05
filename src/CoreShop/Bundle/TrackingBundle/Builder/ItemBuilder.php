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

namespace CoreShop\Bundle\TrackingBundle\Builder;

use CoreShop\Bundle\TrackingBundle\Model\ActionData;
use CoreShop\Bundle\TrackingBundle\Model\ImpressionData;
use CoreShop\Bundle\TrackingBundle\Model\ProductData;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var PurchasablePriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @param PurchasablePriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(PurchasablePriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPurchasableViewItem(PurchasableInterface $product)
    {
        return $this->buildPurchasableActionItem($product);
    }

    /**
     * {@inheritdoc}
     */
    public function buildPurchasableActionItem(PurchasableInterface $product, $quantity = 1)
    {
        $item = new ProductData();

        $item->setId($product->getId());
        $item->setName($product->getName());
        $item->setQuantity($quantity);
        $item->setPrice($this->productPriceCalculator->getPrice($product) / 100);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPurchasableImpressionItem(PurchasableInterface $product)
    {
        $item = new ImpressionData();
        $item->setId($product->getId());
        $item->setName($product->getName());
        $item->setPrice($this->productPriceCalculator->getPrice($product) / 100);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOrderAction(OrderInterface $order)
    {
        $item = new ActionData();
        $item->setId($order->getOrderNumber());
        $item->setRevenue($order->getTotal() / 100);
        $item->setShipping($order->getShipping() / 100);
        $item->setTax($order->getTotalTax() / 100);
        $item->setCurrency($order->getCurrency()->getIsoCode());

        $coupons = [];
        if ($order->getPriceRuleItems() instanceof Fieldcollection) {
            if ($order->getPriceRuleItems()->getCount() > 0) {
                foreach ($order->getPriceRuleItems() as $priceRule) {
                    if ($priceRule instanceof ProposalCartPriceRuleItemInterface) {
                        if ($priceRule->getCartPriceRule() instanceof CartPriceRuleInterface) {
                            $coupons[] = $priceRule->getCartPriceRule()->getName();
                        }
                    }
                }
            }
        }

        if (count($coupons) > 0) {
            $item->setCoupon(implode(', ', $coupons));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCheckoutItems(OrderInterface $order)
    {
        $items = [];

        foreach ($order->getItems() as $item) {
            if ($item instanceof OrderItemInterface) {
                $items[] = $this->buildCheckoutItem($order, $item);
            }
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCheckoutItemsByCart(CartInterface $cart)
    {
        $items = [];

        foreach ($cart->getItems() as $item) {
            $items[] = $this->buildPurchasableActionItem($item->getProduct(), $item->getQuantity());
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCouponByCart(CartInterface $cart)
    {
        $coupons = [];
        if ($cart->getPriceRuleItems() instanceof Fieldcollection) {
            if ($cart->getPriceRuleItems()->getCount() > 0) {
                foreach ($cart->getPriceRuleItems() as $priceRule) {
                    if ($priceRule instanceof ProposalCartPriceRuleItemInterface) {
                        if ($priceRule->getCartPriceRule() instanceof CartPriceRuleInterface) {
                            $coupons[] = $priceRule->getCartPriceRule()->getName();
                        }
                    }
                }
            }
        }

        return implode(', ', $coupons);
    }

    /**
     * {@inheritdoc}
     */
    public function buildCheckoutItem(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $item = new ProductData();
        $item->setId($orderItem->getId());
        $item->setName($orderItem->getProduct()->getName());
        $item->setPrice($orderItem->getItemPrice() / 100);
        $item->setQuantity($orderItem->getQuantity());

        return $item;
    }
}
