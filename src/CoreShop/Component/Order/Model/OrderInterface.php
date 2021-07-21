<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
use Pimcore\Model\DataObject\Objectbrick;

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
    public function getToken(): ?string;

    public function setToken(?string $token);

    public function getBackendCreated(): ?bool;

    public function setBackendCreated(?bool $backendCreated);

    public function getSaleState(): ?string;

    public function setSaleState(?string $saleState);

    public function getOrderState(): ?string;

    public function setOrderState(?string $orderState);

    public function getShippingState(): ?string;

    public function setShippingState(?string $shippingState);

    public function getInvoiceState(): ?string;

    public function setInvoiceState(?string $invoiceState);

    public function getPaymentState(): ?string;

    public function setPaymentState(?string $paymentState);

    public function getOrderDate(): ?Carbon;

    public function setOrderDate(?Carbon $orderDate);

    public function getOrderNumber(): ?string;

    public function setOrderNumber(?string $orderNumber);

    public function getBaseCurrency(): ?CurrencyInterface;

    public function setBaseCurrency(?CurrencyInterface  $currency);

    public function getPaymentTotal(): ?int;

    public function setPaymentTotal(int $paymentTotal);

    public function getTotal(bool $withTax = true): int;

    public function setTotal(int $total, bool $withTax = true);

    public function getTotalTax(): int;

    public function getSubtotal(bool $withTax = true): int;

    public function setSubtotal(int $subtotal, bool $withTax = true);

    public function getSubtotalTax(): int;

    public function getDiscount(bool $withTax = true): int;

    /**
     * @return OrderItemInterface[]
     */
    public function getItems(): ?array;

    /**
     * @param OrderItemInterface[] $items
     */
    public function setItems(?array $items);

    public function hasItems(): bool;

    /**
     * @param OrderItemInterface $item
     */
    public function addItem($item): void;

    /**
     * @param OrderItemInterface $item
     */
    public function removeItem($item): void;

    /**
     * @param OrderItemInterface $item
     *
     * @return bool
     */
    public function hasItem($item): bool;

    public function setConvertedTotal(int $total, bool $withTax = true);

    public function getConvertedTotal(bool $withTax = true): int;

    public function getConvertedPaymentTotal(): ?int;

    public function setConvertedPaymentTotal(?int $convertedPaymentTotal);

    public function getConvertedTotalTax(): int;

    public function getConvertedSubtotal(bool $withTax = true): int;

    public function setConvertedSubtotal(int $subtotal, bool $withTax = true);

    public function getConvertedSubtotalTax(): int;

    public function getConvertedDiscount(bool $withTax = true): int;

    /**
     * @return Fieldcollection
     */
    public function getConvertedTaxes();

    public function setConvertedTaxes(?Fieldcollection $taxes);

    public function getConvertedShipping(bool $withTax = true): int;

    public function getConvertedShippingTax(): int;

    public function getShippingAddress(): ?AddressInterface;

    public function setShippingAddress(?AddressInterface $shippingAddress);

    public function getInvoiceAddress(): ?AddressInterface;

    public function setInvoiceAddress(?AddressInterface $invoiceAddress);

    /**
     * @return Fieldcollection
     */
    public function getTaxes();

    /**
     * @param Fieldcollection $taxes
     */
    public function setTaxes(Fieldcollection $taxes);

    public function getComment(): ?string;

    public function setComment(?string $comment);

    public function getAdditionalData(): ?Objectbrick;

    public function setAdditionalData(?Objectbrick $additionalData);

    /**
     * @return Fieldcollection
     */
    public function getPriceRuleItems();

    /**
     * @param Fieldcollection $priceRuleItems
     */
    public function setPriceRuleItems(Fieldcollection $priceRuleItems);

    /**
     * @return ProposalCartPriceRuleItemInterface[]
     */
    public function getPriceRules(): array;

    public function hasPriceRules(): bool;

    public function addPriceRule(ProposalCartPriceRuleItemInterface $priceRule): void;

    public function removePriceRule(ProposalCartPriceRuleItemInterface $priceRule): void;

    public function hasPriceRule(ProposalCartPriceRuleItemInterface $priceRule): bool;

    public function hasCartPriceRule(
        CartPriceRuleInterface $cartPriceRule,
        CartPriceRuleVoucherCodeInterface $voucherCode = null
    ): bool;

    public function getPriceRuleByCartPriceRule(
        CartPriceRuleInterface $cartPriceRule,
        CartPriceRuleVoucherCodeInterface $voucherCode = null
    ): ?ProposalCartPriceRuleItemInterface;

    public function getPaymentProvider(): ?PaymentProviderInterface;

    public function setPaymentProvider(?PaymentProviderInterface $paymentProvider);
}
