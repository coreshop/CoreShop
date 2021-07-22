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
use Pimcore\Model\DataObject\Fieldcollection;

trait AdjustableTrait
{
    /**
     * {@inheritdoc}
     */
    public function setPimcoreAdjustmentTotalNet($adjustmentTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreAdjustmentTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPimcoreAdjustmentTotalGross($adjustmentTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getPimcoreAdjustmentTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdjustmentItems($adjustmentItems)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustments()
    {
        return $this->getAdjustmentItems() instanceof Fieldcollection && $this->getAdjustmentItems()->getCount() > 0;
    }

    /**
     * @param string|null $type
     *
     * @return AdjustmentInterface[]
     */
    public function getAdjustments(string $type = null)
    {
        $adjustments = [];

        if ($this->getAdjustmentItems() instanceof Fieldcollection) {
            foreach ($this->getAdjustmentItems() as $item) {
                if ($item instanceof AdjustmentInterface) {
                    $adjustments[] = $item;
                }
            }
        }

        if (null === $type) {
            return $adjustments;
        }

        return array_filter(
            $adjustments,
            function (AdjustmentInterface $adjustment) use ($type) {
                return $type === $adjustment->getTypeIdentifier();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment(AdjustmentInterface $adjustment)
    {
        $items = $this->getAdjustmentItems();

        if ($items instanceof Fieldcollection) {
            foreach ($items as $item) {
                if ($item instanceof AdjustmentInterface) {
                    if ($item === $adjustment) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->hasAdjustment($adjustment)) {
            $items = $this->getAdjustmentItems();

            if (!$items instanceof Fieldcollection) {
                $items = new Fieldcollection();
            }

            $items->add($adjustment);

            $this->setAdjustmentItems($items);

            $this->recalculateAdjustmentsTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        $items = $this->getAdjustmentItems();

        if ($items instanceof Fieldcollection) {
            for ($i = 0, $c = $items->getCount(); $i < $c; $i++) {
                $arrayItem = $items->get($i);

                if ($arrayItem === $adjustment) {
                    $items->remove($i);

                    break;
                }
            }

            $this->setAdjustmentItems($items);
            $this->recalculateAdjustmentsTotal();
        }

        $this->addToAdjustmentsTotal($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustments(string $type = null)
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustmentsRecursively(string $type = null)
    {
        $this->removeAdjustments($type);

        if (method_exists($this, 'getItems')) {
            foreach ($this->getItems() as $item) {
                if ($item instanceof AdjustableInterface) {
                    $item->removeAdjustmentsRecursively($type);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotal(?string $type = null, $withTax = true)
    {
        if (null === $type) {
            if ($withTax) {
                return $this->getPimcoreAdjustmentTotalGross() ?: 0;
            }

            return $this->getPimcoreAdjustmentTotalNet() ?: 0;
        }

        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->getNeutral()) {
                $total += $adjustment->getAmount($withTax);
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateAdjustmentsTotal()
    {
        $adjustmentsTotalGross = 0;
        $adjustmentsTotalNet = 0;

        foreach ($this->getAdjustments() as $adjustment) {
            if (!$adjustment->getNeutral()) {
                $adjustmentsTotalGross += $adjustment->getAmount(true);
                $adjustmentsTotalNet += $adjustment->getAmount(false);
            }
        }

        $this->setPimcoreAdjustmentTotalGross($adjustmentsTotalGross);
        $this->setPimcoreAdjustmentTotalNet($adjustmentsTotalNet);

        $this->recalculateAfterAdjustmentChange();
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->getNeutral()) {
            $this->recalculateAdjustmentsTotal();
            $this->recalculateAfterAdjustmentChange();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->getNeutral()) {
            $this->recalculateAdjustmentsTotal();
            $this->recalculateAfterAdjustmentChange();
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function recalculateAfterAdjustmentChange();
}
