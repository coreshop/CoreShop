<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class CartItem extends AbstractPimcoreModel implements CartItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->getProduct()->getWeight() * $this->getQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPrice($withTax = true)
    {
        $product = $this->getProduct();

        if ($product instanceof ProductInterface) {
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

        if ($product instanceof ProductInterface) {
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

        if ($product instanceof ProductInterface) {
            return $product->getWholesalePrice();
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTax()
    {
        $product = $this->getProduct();

        if ($product instanceof ProductInterface) {
            $taxCalculator = $this->getItemTaxCalculator();

            return $taxCalculator->applyTaxes($this->getItemPrice());
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes($applyDiscountToTaxValues = true)
    {
        return \Pimcore::getContainer()->get('coreshop.collector.taxes')->collectTaxes($this->getItemTaxCalculator(), $this->getTotal(false));
    }

    /**
     * @return TaxCalculatorInterface
     */
    private function getItemTaxCalculator() {
        $product = $this->getProduct();

        if ($product instanceof ProductInterface) {
            return $product->getTaxCalculator($this->getCart()->getShippingAddress()); //TODO: Taxation Address should be configurable
        }

        return null;
    }

    /**
     * @return CartInterface
     */
    public function getCart() {
        /**
         * @var $cart CartInterface
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
