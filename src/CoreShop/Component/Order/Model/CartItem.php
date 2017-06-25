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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;


class CartItem extends AbstractPimcoreModel implements CartItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTotalWeight()
    {
        return $this->getItemWeight() * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemWeight()
    {
        return $this->getProduct()->getWeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPrice($withTax = true)
    {
        $product = $this->getProduct();

        if ($product instanceof PurchasableInterface) {
            return $product->getPrice($withTax);
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRetailPrice($withTax = true)
    {
        $product = $this->getProduct();

        if ($product instanceof PurchasableInterface) {
            return $product->getBasePrice($withTax);
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemWholesalePrice()
    {
        $product = $this->getProduct();

        if ($product instanceof PurchasableInterface) {
            return $product->getWholesalePrice();
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTax()
    {
        return $this->getItemPrice(true) - $this->getItemPrice(false);
    }

    /**
     * @return CartInterface
     */
    public function getCart()
    {
        /**
         * @var CartInterface
         */
        $cart = $this->getParent();

        return $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal($withTax = true)
    {
        return $this->getItemPrice($withTax) * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
    {
        return $this->getItemTax() * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsGiftItem()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsGiftItem($isGiftItem)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
