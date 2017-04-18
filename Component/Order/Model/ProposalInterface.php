<?php

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface ProposalInterface extends ResourceInterface
{
    /**
     * @param ProductInterface $product
     * @return CartItemInterface|null
     */
    public function getItemForProduct(ProductInterface $product);

    /**
     * @param boolean $withTax
     * @return float
     */
    public function getTotal($withTax = true);

    /**
     * @return float
     */
    public function getTotalTax();

    /**
     * @param bool $withTax
     * @return float
     */
    public function getSubtotal($withTax = true);

    /**
     * @return float
     */
    public function getSubtotalTax();

    /**
     * @param bool $withTax
     * @return float
     */
    public function getShipping($withTax = true);

    /**
     * @return float
     */
    public function getShippingTaxRate();

    /**
     * @param bool $withTax
     * @return float
     */
    public function getDiscount($withTax = true);

    /**
     * @param boolean $applyDiscountToTaxValues
     * @return mixed
     */
    public function getTaxes($applyDiscountToTaxValues = true);

    /**
     * @param bool $withTax
     * @return float
     */
    public function getPaymentFee($withTax = true);

    /**
     * @return float
     */
    public function getPaymentFeeTaxRate();

    /**
     * @return ProposalItemInterface[]
     */
    public function getItems();

    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @param $item
     */
    public function addItem($item);

    /**
     * @param $item
     */
    public function removeItem($item);

    /**
     * @param $item
     *
     * @return bool
     */
    public function hasItem($item);


    /**
     * @return mixed
     */
    public function getCustomer();

    /**
     * @param $customer
     *
     * @return static
     */
    public function setCustomer($customer);

    /**
     * @return mixed
     */
    public function getShippingAddress();

    /**
     * @param $shippingAddress
     *
     * @return static
     */
    public function setShippingAddress($shippingAddress);

    /**
     * @return mixed
     */
    public function getInvoiceAddress();

    /**
     * @param $invoiceAddress
     *
     * @return static
     */
    public function setInvoiceAddress($invoiceAddress);

    /**
     * @return float
     */
    public function getTotalWeight();
}
