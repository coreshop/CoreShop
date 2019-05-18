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

trait BaseAdjustableTrait
{
    /**
     * {@inheritdoc}
     */
    public function setBasePimcoreAdjustmentTotalNet($adjustmentTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePimcoreAdjustmentTotalNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePimcoreAdjustmentTotalGross($adjustmentTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePimcoreAdjustmentTotalGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAdjustmentItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAdjustmentItems($adjustmentItems)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBaseAdjustments()
    {
        return $this->getBaseAdjustmentItems() instanceof Fieldcollection && $this->getBaseAdjustmentItems()->getCount() > 0;
    }

    /**
     * @param string|null $type
     *
     * @return AdjustmentInterface[]
     */
    public function getBaseAdjustments(string $type = null)
    {
        $adjustments = [];

        if ($this->getBaseAdjustmentItems() instanceof Fieldcollection) {
            foreach ($this->getBaseAdjustmentItems() as $item) {
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
    public function hasBaseAdjustment(AdjustmentInterface $adjustment)
    {
        $items = $this->getBaseAdjustmentItems();

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
    public function addBaseAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->hasBaseAdjustment($adjustment)) {
            $items = $this->getBaseAdjustmentItems();

            if (!$items instanceof Fieldcollection) {
                $items = new Fieldcollection();
            }

            $items->add($adjustment);

            $this->setBaseAdjustmentItems($items);

            $this->recalculateBaseAdjustmentsTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeBaseAdjustment(AdjustmentInterface $adjustment)
    {
        $items = $this->getBaseAdjustmentItems();

        if ($items instanceof Fieldcollection) {
            for ($i = 0, $c = $items->getCount(); $i < $c; $i++) {
                $arrayItem = $items->get($i);

                if ($arrayItem === $adjustment) {
                    $items->remove($i);

                    break;
                }
            }

            $this->setBaseAdjustmentItems($items);
            $this->recalculateBaseAdjustmentsTotal();
        }

        $this->addToBaseAdjustmentsTotal($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function removeBaseAdjustments(string $type = null)
    {
        foreach ($this->getBaseAdjustments($type) as $adjustment) {
            $this->removeBaseAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeBaseAdjustmentsRecursively(string $type = null)
    {
        $this->removeBaseAdjustments($type);

        if (method_exists($this, 'getItems')) {
            foreach ($this->getItems() as $item) {
                if ($item instanceof BaseAdjustableInterface) {
                    $item->removeBaseAdjustmentsRecursively($type);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAdjustmentsTotal(?string $type = null, $withTax = true)
    {
        if (null === $type) {
            if ($withTax) {
                return $this->getBasePimcoreAdjustmentTotalGross() ?: 0;
            }

            return $this->getBasePimcoreAdjustmentTotalNet() ?: 0;
        }

        $total = 0;
        foreach ($this->getBaseAdjustments($type) as $adjustment) {
            if (!$adjustment->getNeutral()) {
                $total += $adjustment->getAmount($withTax);
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateBaseAdjustmentsTotal()
    {
        $adjustmentsTotalGross = 0;
        $adjustmentsTotalNet = 0;

        foreach ($this->getBaseAdjustments() as $adjustment) {
            if (!$adjustment->getNeutral()) {
                $adjustmentsTotalGross += $adjustment->getAmount(true);
                $adjustmentsTotalNet += $adjustment->getAmount(false);
            }
        }

        $this->setBasePimcoreAdjustmentTotalGross($adjustmentsTotalGross);
        $this->setBasePimcoreAdjustmentTotalNet($adjustmentsTotalNet);

        $this->recalculateBaseAfterAdjustmentChange();
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function addToBaseAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->getNeutral()) {
            $this->recalculateBaseAdjustmentsTotal();
            $this->recalculateBaseAfterAdjustmentChange();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function subtractFromBaseAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->getNeutral()) {
            $this->recalculateBaseAdjustmentsTotal();
            $this->recalculateBaseAfterAdjustmentChange();
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function recalculateBaseAfterAdjustmentChange();
}
