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

    /**
     * {@inheritdoc}
     */
    public function hasItems()
    {
        return is_array($this->getItems()) && count($this->getItems()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem($item)
    {
        Assert::isInstanceOf($item, OrderItemInterface::class);

        $items = $this->getItems();
        $items[] = $item;

        $this->setItems($items);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem($item)
    {
        $items = $this->getItems();

        for ($i = 0, $c = count($items); $i < $c; $i++) {
            $arrayItem = $items[$i];

            if ($arrayItem->getId() === $item->getId()) {
                unset($items[$i]);

                break;
            }
        }

        $this->setItems(array_values($items));
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($item)
    {
        $items = $this->getItems();

        for ($i = 0, $c = count($items); $i < $c; $i++) {
            $arrayItem = $items[$i];

            if ($arrayItem->getId() === $item->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getSaleState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $saleState
     */
    public function setSaleState($saleState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return string
     */
    public function getOrderState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $orderState
     */
    public function setOrderState($orderState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return string
     */
    public function getShippingState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $shippingState
     */
    public function setShippingState($shippingState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return string
     */
    public function getInvoiceState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $invoiceState
     */
    public function setInvoiceState($invoiceState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return string
     */
    public function getPaymentState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param string $paymentState
     */
    public function setPaymentState($paymentState)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderDate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderDate($orderDate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderNumber()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderNumber($orderNumber)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider($paymentProvider)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($store)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer($customer)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress($shippingAddress)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceAddress()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceAddress($invoiceAddress)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxes($taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalData()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalData($additionalData)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleCode($localeCode)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax(): int
    {
        return $this->getTotal(true) - $this->getTotal(false);
    }

    /**
     * @return int
     */
    public function getConvertedTotalTax(): int
    {
        return $this->getConvertedTotal(true) - $this->getConvertedTotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalTax(): int
    {
        return $this->getSubtotal(true) - $this->getSubtotal(false);
    }

    /**
     * @return int
     */
    public function getConvertedSubtotalTax(): int
    {
        return $this->getConvertedSubtotal(true) - $this->getConvertedSubtotal(false);
    }

    /**
     * @return int
     */
    public function getConvertedShippingTax(): int
    {
        return $this->getConvertedShipping(true) - $this->getConvertedShipping(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrency()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseCurrency($currency)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getDiscount(bool $withTax = true): int
    {
        return $this->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getSubtotalGross() : $this->getSubtotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setSubtotal(int $subtotal, bool $withTax = true)
    {
        return $withTax ? $this->setSubtotalGross($subtotal) : $this->setSubtotalNet($subtotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setTotal(int $total, bool $withTax = true)
    {
        return $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentTotal()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentTotal(int $paymentTotal)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalNet(int $subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalGross(int $subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingTaxRate($taxRate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getConvertedDiscount(bool $withTax = true): int
    {
        return $this->getConvertedAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getConvertedSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedSubtotalGross() : $this->getConvertedSubtotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setConvertedSubtotal(int $subtotal, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedSubtotalGross($subtotal) : $this->setConvertedSubtotalNet($subtotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getConvertedTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedTotalGross() : $this->getConvertedTotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setConvertedTotal(int $total, bool $withTax = true)
    {
        return $withTax ? $this->setConvertedTotalGross($total) : $this->setConvertedTotalNet($total);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getConvertedShipping(bool $withTax = true): int
    {
        return $this->getConvertedAdjustmentsTotal(AdjustmentInterface::SHIPPING, $withTax);
    }

    /**
     * @return int
     */
    public function getConvertedTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $total
     */
    public function setConvertedTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return int
     */
    public function getConvertedTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $total
     */
    public function setConvertedTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedPaymentTotal()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedPaymentTotal(int $convertedPaymentTotal)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return int
     */
    public function getConvertedSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $subTotalNet
     */
    public function setConvertedSubtotalNet(int $subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return int
     */
    public function getConvertedSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $subTotalGross
     */
    public function setConvertedSubtotalGross(int $subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return Fieldcollection
     */
    public function getConvertedTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param Fieldcollection $taxes
     */
    public function setConvertedTaxes($taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendCreated()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBackendCreated($backendCreated)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    protected function recalculateConvertedAfterAdjustmentChange()
    {
        $this->setConvertedTotal($this->getConvertedSubtotal(true) + $this->getConvertedAdjustmentsTotal(null, true), true);
        $this->setConvertedTotal($this->getConvertedSubtotal(false) + $this->getConvertedAdjustmentsTotal(null, false), false);
    }

    /**
     * {@inheritdoc}
     */
    protected function recalculateAfterAdjustmentChange()
    {
        $this->setTotal($this->getSubtotal(true) + $this->getAdjustmentsTotal(null, true), true);
        $this->setTotal($this->getSubtotal(false) + $this->getAdjustmentsTotal(null, false), false);
    }
}
