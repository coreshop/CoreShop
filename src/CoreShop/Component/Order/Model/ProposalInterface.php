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

use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

interface ProposalInterface extends ResourceInterface, CurrencyAwareInterface, StoreAwareInterface
{
    /**
     * @param PurchasableInterface $product
     *
     * @return CartItemInterface|null
     */
    public function getItemForProduct(PurchasableInterface $product);

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
     * @param bool $withTax
     *
     * @return float
     */
    public function getSubtotal($withTax = true);

    /**
     * @return float
     */
    public function getSubtotalTax();

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getDiscount($withTax = true);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getPaymentFee($withTax = true);

    /**
     * @return mixed
     */
    public function getPaymentProvider();

    /**
     * @param $paymentProvider
     *
     * @return mixed
     */
    public function setPaymentProvider($paymentProvider);

    /**
     * @return float
     */
    public function getPaymentFeeTaxRate();

    /**
     * @return CartItemInterface[]
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
    public function getWeight();
}
