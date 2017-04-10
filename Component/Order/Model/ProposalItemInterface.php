<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProposalItemInterface extends ResourceInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param $product
     */
    public function setProduct($product);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param $quantity
     *
     * @return int
     */
    public function setQuantity($quantity);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getItemPrice($withTax = true);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getItemRetailPrice($withTax = true);

    /**
     * @return float
     */
    public function getItemWholesalePrice();

    /**
     * @return float
     */
    public function getItemTax();

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @return float
     */
    public function getTotalTax();

    /**
     * @return boolean
     */
    public function getIsGiftItem();
}
