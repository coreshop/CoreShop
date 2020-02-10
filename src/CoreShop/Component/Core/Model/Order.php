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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\Order as BaseOrder;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Shipping\Model\CarrierAwareTrait;

abstract class Order extends BaseOrder implements OrderInterface
{
    use SaleTrait;
    use CarrierAwareTrait;

     use CarrierAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        $weight = 0;

        foreach ($this->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                $weight += $item->getTotalWeight();
            }
        }

        return $weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getShipping($withTax = true)
    {
        return $withTax ? $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, true) : $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, false);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTax()
    {
        return $this->getShipping(true) - $this->getShipping(false);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippableItems()
    {
        $shippable = false;
        /** @var SaleItemInterface $item */
        foreach ($this->getItems() as $item) {
            if ($item->getDigitalProduct() !== true) {
                $shippable = true;

                break;
            }
        }

        return $shippable;
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
    public function setShippingTaxRate($shippingTaxRate)
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
    public function getPaymentSettings()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentSettings($paymentSettings)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedsRecalculation()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setNeedsRecalculation($needsRecalculation)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
