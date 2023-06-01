<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\CurrencyAwareTrait;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

abstract class Order extends AbstractPimcoreModel implements OrderInterface
{
    use StoreAwareTrait;
    use CurrencyAwareTrait;
    use AdjustableTrait;
    use ProposalPriceRuleTrait;
    use ConvertedAdjustableTrait;

    public function hasItems(): bool
    {
        return is_array($this->getItems()) && count($this->getItems()) > 0;
    }

    public function addItem($item): void
    {
        /**
         * @var OrderItemInterface $item
         */
        Assert::isInstanceOf($item, OrderItemInterface::class);

        $item->setOrder($this);

        $items = $this->getItems();
        $items[] = $item;

        $this->setItems($items);
    }

    public function removeItem($item): void
    {
        $items = $this->getItems();

        foreach ($items as $i => $iValue) {
            $arrayItem = $iValue;

            if ($arrayItem->getId() === $item->getId()) {
                unset($items[$i]);

                break;
            }
        }

        $this->setItems(array_values($items));
    }

    public function hasItem($item): bool
    {
        $items = $this->getItems();

        foreach ($items as $iValue) {
            $arrayItem = $iValue;

            if ($arrayItem->getId() === $item->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getTotalTax(): int
    {
        return $this->getTotal(true) - $this->getTotal(false);
    }

    public function getConvertedTotalTax(): int
    {
        return $this->getConvertedTotal(true) - $this->getConvertedTotal(false);
    }

    public function getSubtotalTax(): int
    {
        return $this->getSubtotal(true) - $this->getSubtotal(false);
    }

    public function getConvertedSubtotalTax(): int
    {
        return $this->getConvertedSubtotal(true) - $this->getConvertedSubtotal(false);
    }

    public function getConvertedShippingTax(): int
    {
        return $this->getConvertedShipping(true) - $this->getConvertedShipping(false);
    }

    public function getDiscount(bool $withTax = true): int
    {
        return $this->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    public function getSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getSubtotalGross() : $this->getSubtotalNet();
    }

    public function setSubtotal(int $subtotal, bool $withTax = true)
    {
        $withTax ? $this->setSubtotalGross($subtotal) : $this->setSubtotalNet($subtotal);
    }

    public function getTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    public function setTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    public function getConvertedDiscount(bool $withTax = true): int
    {
        return $this->getConvertedAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    public function getConvertedSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedSubtotalGross() : $this->getConvertedSubtotalNet();
    }

    public function setConvertedSubtotal(int $subtotal, bool $withTax = true)
    {
        $withTax ? $this->setConvertedSubtotalGross($subtotal) : $this->setConvertedSubtotalNet($subtotal);
    }

    public function getConvertedTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedTotalGross() : $this->getConvertedTotalNet();
    }

    public function setConvertedTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setConvertedTotalGross($total) : $this->setConvertedTotalNet($total);
    }

    public function getConvertedShipping(bool $withTax = true): int
    {
        return $this->getConvertedAdjustmentsTotal(AdjustmentInterface::SHIPPING, $withTax);
    }

    public function getConvertedPaymentProviderFee(): int
    {
        return $this->getConvertedAdjustmentsTotal(AdjustmentInterface::PAYMENT, false);
    }

    public function getTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getPaymentTotal(): ?int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setPaymentTotal(?int $paymentTotal)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setSubtotalNet(int $subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setSubtotalGross(int $subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedPaymentTotal(): ?int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedPaymentTotal(?int $convertedPaymentTotal)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedSubtotalNet(int $subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedSubtotalGross(int $subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTaxes(?Fieldcollection $taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    protected function recalculateConvertedAfterAdjustmentChange(): void
    {
        $this->setConvertedTotal($this->getConvertedSubtotal(true) + $this->getConvertedAdjustmentsTotal(null, true), true);
        $this->setConvertedTotal($this->getConvertedSubtotal(false) + $this->getConvertedAdjustmentsTotal(null, false), false);
    }

    protected function recalculateAfterAdjustmentChange(): void
    {
        $this->setTotal($this->getSubtotal(true) + $this->getAdjustmentsTotal(null, true), true);
        $this->setTotal($this->getSubtotal(false) + $this->getAdjustmentsTotal(null, false), false);
    }
}
