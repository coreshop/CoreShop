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

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Currency\Model\CurrencyAwareInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Customer\Model\CustomerAwareInterface;
use CoreShop\Component\Locale\Model\LocaleAwareInterface;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use Pimcore\Model\DataObject\Fieldcollection;

interface OrderInterface extends
    PimcoreModelInterface,
    CurrencyAwareInterface,
    StoreAwareInterface,
    LocaleAwareInterface,
    AdjustableInterface,
    ConvertedAdjustableInterface,
    CustomerAwareInterface,
    PayableInterface,
    StorageListInterface
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return bool
     */
    public function getBackendCreated();

    /**
     * @param bool $backendCreated
     */
    public function setBackendCreated($backendCreated);

    /**
     * @return string
     */
    public function getSaleState();

    /**
     * @param string $saleState
     */
    public function setSaleState($saleState);

    /**
     * @return string
     */
    public function getOrderState();

    /**
     * @param string $orderState
     */
    public function setOrderState($orderState);

    /**
     * @return string
     */
    public function getShippingState();

    /**
     * @param string $shippingState
     */
    public function setShippingState($shippingState);

    /**
     * @return string
     */
    public function getInvoiceState();

    /**
     * @param string $invoiceState
     */
    public function setInvoiceState($invoiceState);

    /**
     * @return string
     */
    public function getPaymentState();

    /**
     * @param string $paymentState
     */
    public function setPaymentState($paymentState);

    /**
     * @return Carbon
     */
    public function getOrderDate();

    /**
     * @param Carbon $orderDate
     */
    public function setOrderDate($orderDate);

    /**
     * @return string
     */
    public function getOrderNumber();

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     *
     * @return mixed
     */
    public function setCurrency($currency);

    /**
     * @return CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @param CurrencyInterface $currency
     *
     * @return mixed
     */
    public function setBaseCurrency($currency);

    /**
     * @return int
     */
    public function getPaymentTotal();

    /**
     * @param int $paymentTotal
     */
    public function setPaymentTotal(int $paymentTotal);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal(bool $withTax = true): int;

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setTotal(int $total, bool $withTax = true);

    /**
     * @return int
     */
    public function getTotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getSubtotal(bool $withTax = true): int;

    /**
     * @param int  $subtotal
     * @param bool $withTax
     * @return mixed
     */
    public function setSubtotal(int $subtotal, bool $withTax = true);

    /**
     * @return int
     */
    public function getSubtotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getDiscount(bool $withTax = true): int;

    /**
     * @return OrderItemInterface[]
     */
    public function getItems();

    /**
     * @param OrderItemInterface[] $items
     */
    public function setItems($items);

    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @param OrderItemInterface $item
     */
    public function addItem($item);

    /**
     * @param OrderItemInterface $item
     */
    public function removeItem($item);

    /**
     * @param OrderItemInterface $item
     *
     * @return bool
     */
    public function hasItem($item);

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setConvertedTotal(int $total, bool $withTax = true);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getConvertedTotal(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getConvertedPaymentTotal();

    /**
     * @param int $convertedPaymentTotal
     */
    public function setConvertedPaymentTotal(int $convertedPaymentTotal);

    /**
     * @return int
     */
    public function getConvertedTotalTax(): int;

    /**
     * @param bool $withTax
     * @return int
     */
    public function getConvertedSubtotal(bool $withTax = true): int;
    /**
     * @param int  $subtotal
     * @param bool $withTax
     */
    public function setConvertedSubtotal(int $subtotal, bool $withTax = true);

    /**
     * @return int
     */
    public function getConvertedSubtotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedDiscount(bool $withTax = true): int;

    /**
     * @return Fieldcollection
     */
    public function getConvertedTaxes();

    /**
     * @param Fieldcollection $taxes
     */
    public function setConvertedTaxes($taxes);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedShipping(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getConvertedShippingTax(): int;

    /**
     * @return AddressInterface|null
     */
    public function getShippingAddress();

    /**
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress($shippingAddress);

    /**
     * @return AddressInterface|null
     */
    public function getInvoiceAddress();

    /**
     * @param AddressInterface $invoiceAddress
     */
    public function setInvoiceAddress($invoiceAddress);

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

    /**
     * @return Fieldcollection
     */
    public function getPriceRuleItems();

    /**
     * @param Fieldcollection $priceRuleItems
     */
    public function setPriceRuleItems($priceRuleItems);

    /**
     * @return ProposalCartPriceRuleItemInterface[]
     */
    public function getPriceRules();

    /**
     * @return bool
     */
    public function hasPriceRules();

    /**
     * @param ProposalCartPriceRuleItemInterface $priceRule
     */
    public function addPriceRule(ProposalCartPriceRuleItemInterface $priceRule);

    /**
     * @param ProposalCartPriceRuleItemInterface $priceRule
     */
    public function removePriceRule(ProposalCartPriceRuleItemInterface $priceRule);

    /**
     * @param ProposalCartPriceRuleItemInterface $priceRule
     *
     * @return bool
     */
    public function hasPriceRule(ProposalCartPriceRuleItemInterface $priceRule);

    /**
     * @param CartPriceRuleInterface                 $cartPriceRule
     * @param CartPriceRuleVoucherCodeInterface|null $voucherCode
     *
     * @return bool
     */
    public function hasCartPriceRule(
        CartPriceRuleInterface $cartPriceRule,
        CartPriceRuleVoucherCodeInterface $voucherCode = null
    );

    /**
     * @param CartPriceRuleInterface                 $cartPriceRule
     * @param CartPriceRuleVoucherCodeInterface|null $voucherCode
     *
     * @return ProposalCartPriceRuleItemInterface|null
     */
    public function getPriceRuleByCartPriceRule(
        CartPriceRuleInterface $cartPriceRule,
        CartPriceRuleVoucherCodeInterface $voucherCode = null
    );

    /**
     * @return PaymentProviderInterface
     */
    public function getPaymentProvider();

    /**
     * @param PaymentProviderInterface $paymentProvider
     *
     * @return PaymentProviderInterface
     */
    public function setPaymentProvider($paymentProvider);
}
