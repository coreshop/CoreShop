<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use Pimcore\Model\DataObject\Fieldcollection;

abstract class Sale extends AbstractProposal implements SaleInterface
{
    use ProposalPriceRuleTrait;
    use BaseAdjustableTrait;

    /**
     * {@inheritdoc}
     */
    public function getTotalTax()
    {
        return $this->getTotal(true) - $this->getTotal(false);
    }

    /**
     * @return float
     */
    public function getBaseTotalTax()
    {
        return $this->getBaseTotal(true) - $this->getBaseTotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalTax()
    {
        return $this->getSubtotal(true) - $this->getSubtotal(false);
    }

    /**
     * @return float
     */
    public function getBaseSubtotalTax()
    {
        return $this->getBaseSubtotal(true) - $this->getBaseSubtotal(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTax()
    {
        return $this->getShipping(true) - $this->getShipping(false);
    }

    /**
     * @return float
     */
    public function getBaseShippingTax()
    {
        return $this->getBaseShipping(true) - $this->getBaseShipping(false);
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
    public function getDiscount($withTax = true)
    {
        return $this->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getSubtotal($withTax = true)
    {
        return $withTax ? $this->getSubtotalGross() : $this->getSubtotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setSubtotal($subtotal, $withTax = true)
    {
        return $withTax ? $this->setSubtotalGross($subtotal) : $this->setSubtotalNet($subtotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getTotal($withTax = true)
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setTotal($total, $withTax = true)
    {
        return $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getShipping($withTax = true)
    {
        return $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalNet($subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalGross($subTotalGross)
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
    public function getBaseDiscount($withTax = true)
    {
        return $this->getBaseAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $withTax);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getBaseSubtotal($withTax = true)
    {
        return $withTax ? $this->getBaseSubtotalGross() : $this->getBaseSubtotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setBaseSubtotal($subtotal, $withTax = true)
    {
        $withTax ? $this->setBaseSubtotalGross($subtotal) : $this->setBaseSubtotalNet($subtotal);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getBaseTotal($withTax = true)
    {
        return $withTax ? $this->getBaseTotalGross() : $this->getBaseTotalNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setBaseTotal($total, $withTax = true)
    {
        $withTax ? $this->setBaseTotalGross($total) : $this->setBaseTotalNet($total);
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function getBaseShipping($withTax = true)
    {
        return $this->getBaseAdjustmentsTotal(AdjustmentInterface::SHIPPING, $withTax);
    }

    /**
     * @return float
     */
    public function getBaseTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $total
     */
    public function setBaseTotalNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $total
     */
    public function setBaseTotalGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseSubtotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $subTotalNet
     */
    public function setBaseSubtotalNet($subTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseSubtotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param int $subTotalGross
     */
    public function setBaseSubtotalGross($subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return Fieldcollection
     */
    public function getBaseTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param Fieldcollection $taxes
     */
    public function setBaseTaxes($taxes)
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
    protected function recalculateBaseAfterAdjustmentChange()
    {
    }
}
