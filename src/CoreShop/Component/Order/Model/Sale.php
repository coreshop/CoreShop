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

use CoreShop\Component\Resource\ImplementedByPimcoreException;
use Pimcore\Model\DataObject\Fieldcollection;

abstract class Sale extends AbstractProposal implements SaleInterface
{
    use ProposalPriceRuleTrait;

    /**
     * {@inheritdoc}
     */
    public function getItemForProduct(PurchasableInterface $product)
    {
        foreach ($this->getItems() as $item) {
            if ($item instanceof ProposalItemInterface) {
                if ($item->getProduct() instanceof PurchasableInterface && $item->getProduct()->getId() === $product->getId()) {
                    return $item;
                }
            }
        }

        return null;
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
        return $withTax ? $this->getDiscountGross() : $this->getDiscountNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setDiscount($discount, $withTax = true)
    {
        return $withTax ? $this->setDiscountGross($discount) : $this->setDiscountNet($discount);
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
        return $withTax ? $this->getShippingGross() : $this->getShippingNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setShipping($shipping, $withTax = true)
    {
        return $withTax ? $this->setShippingGross($shipping) : $this->setShippingNet($shipping);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPercentage()
    {
        $totalWithoutDiscount = $this->getSubtotal(false);
        $totalWithDiscount = $this->getSubtotal(false) - $this->getDiscount(false);

        if ($totalWithoutDiscount > 0) {
            return ((100 / $totalWithoutDiscount) * $totalWithDiscount) / 100;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
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
    public function getTotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalTax($totalTax)
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
    public function getSubtotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtotalTax($subtotalTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingTax($shippingTax)
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
     * {@inheritdoc}
     */
    public function getDiscountNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountGross($total)
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
        return $withTax ? $this->getBaseDiscountGross() : $this->getBaseDiscountNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setBaseDiscount($discount, $withTax = true)
    {
        $withTax ? $this->setBaseDiscountGross($discount) : $this->setBaseDiscountNet($discount);
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
        return $withTax ? $this->getBaseShippingGross() : $this->getBaseShippingNet();
    }

    /**
     * Wrapper Method for Pimcore Object.
     *
     * {@inheritdoc}
     */
    public function setBaseShipping($shipping, $withTax = true)
    {
        $withTax ? $this->setBaseShippingGross($shipping) : $this->setBaseShippingNet($shipping);
    }

    /**
     * @return float
     */
    public function getBaseTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $total
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
     * @param $total
     */
    public function setBaseTotalGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseTotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $totalTax
     */
    public function setBaseTotalTax($totalTax)
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
     * @param $subTotalNet
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
     * @param $subTotalGross
     */
    public function setBaseSubtotalGross($subTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseSubtotalTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseShippingNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $subtotalTax
     */
    public function setBaseSubtotalTax($subtotalTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $total
     */
    public function setBaseShippingNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $total
     */
    public function setBaseShippingGross($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseShippingTax()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $shippingTax
     */
    public function setBaseShippingTax($shippingTax)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseDiscountNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @param $total
     */
    public function setBaseDiscountNet($total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function getBaseDiscountGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @return float
     */
    public function setBaseDiscountGross($total)
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
     * @param $taxes
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
    public function getNotifyCustomer()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setNotifyCustomer($notifyCustomer)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
