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
use Pimcore\Model\DataObject\Fieldcollection;

trait ConvertedAdjustableTrait
{
    /**
     * {@inheritdoc}
     */
    public function setConvertedPimcoreAdjustmentTotalNet(int $adjustmentTotalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedPimcoreAdjustmentTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedPimcoreAdjustmentTotalGross(int $adjustmentTotalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedPimcoreAdjustmentTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getConvertedAdjustmentItems()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setConvertedAdjustmentItems($adjustmentItems)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function removeConvertedAdjustments(string $type = null)
    {
        foreach ($this->getConvertedAdjustments($type) as $adjustment) {
            $this->removeConvertedAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
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
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    abstract protected function recalculateConvertedAfterAdjustmentChange();
}
