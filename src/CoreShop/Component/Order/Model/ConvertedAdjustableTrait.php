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

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use Pimcore\Model\DataObject\Fieldcollection;

trait ConvertedAdjustableTrait
{
    public function setConvertedPimcoreAdjustmentTotalNet(int $adjustmentTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedPimcoreAdjustmentTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedPimcoreAdjustmentTotalGross(int $adjustmentTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedPimcoreAdjustmentTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedAdjustmentItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedAdjustmentItems(?Fieldcollection $adjustmentItems)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function hasConvertedAdjustments()
    {
        return $this->getConvertedAdjustmentItems() instanceof Fieldcollection && $this->getConvertedAdjustmentItems()->getCount() > 0;
    }

    /**
     * @param string|null $type
     *
     * @return AdjustmentInterface[]
     */
    public function getConvertedAdjustments(string $type = null)
    {
        $adjustments = [];

        if ($this->getConvertedAdjustmentItems() instanceof Fieldcollection) {
            foreach ($this->getConvertedAdjustmentItems() as $item) {
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

    public function hasConvertedAdjustment(AdjustmentInterface $adjustment)
    {
        $items = $this->getConvertedAdjustmentItems();

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

    public function addConvertedAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->hasConvertedAdjustment($adjustment)) {
            $items = $this->getConvertedAdjustmentItems();

            if (!$items instanceof Fieldcollection) {
                $items = new Fieldcollection();
            }

            $items->add($adjustment);

            $this->setConvertedAdjustmentItems($items);

            $this->recalculateConvertedAdjustmentsTotal();
        }
    }

    public function removeConvertedAdjustment(AdjustmentInterface $adjustment)
    {
        $items = $this->getConvertedAdjustmentItems();

        if ($items instanceof Fieldcollection) {
            for ($i = 0, $c = $items->getCount(); $i < $c; $i++) {
                $arrayItem = $items->get($i);

                if ($arrayItem === $adjustment) {
                    $items->remove($i);

                    break;
                }
            }

            $this->setConvertedAdjustmentItems($items);
            $this->recalculateConvertedAdjustmentsTotal();
        }

        $this->addToConvertedAdjustmentsTotal($adjustment);
    }

    public function removeConvertedAdjustments(string $type = null)
    {
        foreach ($this->getConvertedAdjustments($type) as $adjustment) {
            $this->removeConvertedAdjustment($adjustment);
        }
    }

    public function removeConvertedAdjustmentsRecursively(string $type = null)
    {
        $this->removeConvertedAdjustments($type);

        if (method_exists($this, 'getItems')) {
            foreach ($this->getItems() as $item) {
                if ($item instanceof ConvertedAdjustableInterface) {
                    $item->removeConvertedAdjustmentsRecursively($type);
                }
            }
        }

        if (method_exists($this, 'getUnits')) {
            foreach ($this->getUnits() as $item) {
                if ($item instanceof ConvertedAdjustableInterface) {
                    $item->removeConvertedAdjustmentsRecursively($type);
                }
            }
        }
    }

    public function getConvertedAdjustmentsTotal(?string $type = null, bool $withTax = true): int
    {
        if (null === $type) {
            if ($withTax) {
                return $this->getConvertedPimcoreAdjustmentTotalGross() ?: 0;
            }

            return $this->getConvertedPimcoreAdjustmentTotalNet() ?: 0;
        }

        $total = 0;
        foreach ($this->getConvertedAdjustments($type) as $adjustment) {
            if (!$adjustment->getNeutral()) {
                $total += $adjustment->getAmount($withTax);
            }
        }

        return $total;
    }

    public function recalculateConvertedAdjustmentsTotal()
    {
        $adjustmentsTotalGross = 0;
        $adjustmentsTotalNet = 0;

        foreach ($this->getConvertedAdjustments() as $adjustment) {
            if (!$adjustment->getNeutral()) {
                $adjustmentsTotalGross += $adjustment->getAmount(true);
                $adjustmentsTotalNet += $adjustment->getAmount(false);
            }
        }

        $this->setConvertedPimcoreAdjustmentTotalGross($adjustmentsTotalGross);
        $this->setConvertedPimcoreAdjustmentTotalNet($adjustmentsTotalNet);

        $this->recalculateConvertedAfterAdjustmentChange();
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function addToConvertedAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->getNeutral()) {
            $this->recalculateConvertedAdjustmentsTotal();
            $this->recalculateConvertedAfterAdjustmentChange();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function subtractFromConvertedAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->getNeutral()) {
            $this->recalculateConvertedAdjustmentsTotal();
            $this->recalculateConvertedAfterAdjustmentChange();
        }
    }

    abstract protected function recalculateConvertedAfterAdjustmentChange(): void;
}
