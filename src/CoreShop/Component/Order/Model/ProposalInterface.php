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
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Locale\Model\LocaleAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use Pimcore\Model\DataObject\Fieldcollection;

interface ProposalInterface extends ResourceInterface, CurrencyAwareInterface, StoreAwareInterface, LocaleAwareInterface
{

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     * @return mixed
     */
    public function setCurrency($currency);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal($withTax = true);

    /**
     * @return int
     */
    public function getTotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getSubtotal($withTax = true);

    /**
     * @return int
     */
    public function getSubtotalTax();

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getDiscount($withTax = true);

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
    public function getWeight();

    /**
     * @return Fieldcollection
     */
    public function getTaxes();

    /**
     * @param Fieldcollection $taxes
     */
    public function setTaxes($taxes);

    /**
     * @return string|null
     */
    public function getComment();

    /**
     * @param string $comment
     */
    public function setComment($comment);

    /**
     * @return \Pimcore\Model\DataObject\Objectbrick|null
     */
    public function getAdditionalData();

    /**
     * @param \Pimcore\Model\DataObject\Objectbrick $additionalData
     */
    public function setAdditionalData($additionalData);

}
