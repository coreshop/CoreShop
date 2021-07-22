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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

class Adjustment extends AbstractPimcoreFieldcollection implements AdjustmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getObject()->getId() . '_tax_item_' . $this->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustable()
    {
        if ($this->getObject() instanceof AdjustableInterface) {
            return $this->getObject();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount($withTax = true)
    {
        return $withTax ? $this->getPimcoreAmountGross() : $this->getPimcoreAmountNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(int $grossAmount, int $netAmount)
    {
        $this->setPimcoreAmountGross($grossAmount);
        $this->setPimcoreAmountNet($netAmount);

        if (!$this->getNeutral()) {
            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNeutral()
    {
        return $this->getPimcoreNeutral();
    }

    /**
     * {@inheritdoc}
     */
    public function setNeutral(bool $neutral)
    {
        if ($this->getPimcoreNeutral() !== $neutral) {
            $this->setPimcoreNeutral($neutral);

            $this->recalculateAdjustable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCharge()
    {
        return 0 > $this->getAmount();
    }

    /**
     * {@inheritdoc}
     */
    public function isCredit()
    {
        return 0 < $this->getAmount();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeIdentifier()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeIdentifier($typeIdentifier)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreAmountNet($pimcoreAmountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreAmountNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreAmountGross($pimcoreAmountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreAmountGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreNeutral($pimcoreNeutral)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreNeutral()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    private function recalculateAdjustable()
    {
        $adjustable = $this->getAdjustable();
        if (null !== $adjustable) {
            $adjustable->recalculateAdjustmentsTotal();
        }
    }
}
