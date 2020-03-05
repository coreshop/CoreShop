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

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class OrderInvoice extends AbstractPimcoreModel implements OrderInvoiceInterface
{
    use AdjustableTrait;
    use BaseAdjustableTrait;

    /**
     * {@inheritdoc}
     */
    public static function getDocumentType()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalTax(): int
    {
        return $this->getTotal(true) - $this->getTotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotalTax(): int
    {
        return $this->getBaseTotal(true) - $this->getBaseTotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountTax(): int
    {
        return $this->getDiscount(true) - $this->getDiscount(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTax(): int
    {
        return $this->getShipping(true) - $this->getShipping(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalTax(): int
    {
        return $this->getSubtotal(true) - $this->getSubtotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDiscountTax(): int
    {
        return $this->getBaseDiscount(true) - $this->getBaseDiscount(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseShippingTax(): int
    {
        return $this->getBaseShipping(true) - $this->getBaseShipping(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseSubtotalTax(): int
    {
        return $this->getBaseSubtotal(true) - $this->getBaseSubtotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($order)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderedAsset()
    {
        return $this->getProperty('rendered_asset');
    }

    /**
     * {@inheritdoc}
     */
    public function setRenderedAsset($renderedAsset)
    {
        $this->setProperty('rendered_asset', 'asset', $renderedAsset);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentDate()
    {
        return $this->getInvoiceDate();
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentDate($documentDate)
    {
        return $this->setInvoiceDate($documentDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentNumber()
    {
        return $this->getInvoiceNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentNumber($documentNumber)
    {
        return $this->setInvoiceNumber($documentNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceDate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceDate($invoiceDate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceNumber()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRuleItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRuleItems($priceRuleItems)
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
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getShipping(bool $withTax = true): int
    {
        return $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, $withTax);
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
    public function getShippingNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTaxRate()
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
    public function getBaseDiscount(bool $withTax = true): int
    {
        return $this->getBaseAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getBaseSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseSubtotalGross() : $this->getBaseSubtotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setBaseSubtotal(int $baseSubtotal, bool $withTax = true)
    {
        return $withTax ? $this->setBaseSubtotalGross($baseSubtotal) : $this->setBaseSubtotalNet($baseSubtotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getBaseTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getBaseTotalGross() : $this->getBaseTotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setBaseTotal(int $baseTotal, bool $withTax = true)
    {
        return $withTax ? $this->setBaseTotalGross($baseTotal) : $this->setBaseTotalNet($baseTotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getBaseShipping(bool $withTax = true): int
    {
        return $this->getBaseAdjustmentsTotal(AdjustmentInterface::SHIPPING, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTotalGross(int $baseTotal)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseSubtotalNet(int $baseSubTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseSubtotalGross(int $baseSubTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    protected function recalculateAfterAdjustmentChange()
    {
    }

    protected function recalculateBaseAfterAdjustmentChange()
    {
    }
}
