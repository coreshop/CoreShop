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
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use CoreShop\Component\Taxation\Model\TaxItemInterface;
use Pimcore\Model\DataObject\Fieldcollection;

abstract class OrderItemUnit extends AbstractPimcoreModel implements OrderItemUnitInterface
{
    use AdjustableTrait;
    use ConvertedAdjustableTrait;

    public function getOrderItem(): OrderItemInterface
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof OrderItemInterface) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent !== null);

        throw new \Exception('Order Item Unit does not have a valid Order Item');
    }

    public function getTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getTotalGross() : $this->getTotalNet();
    }

    public function setTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setTotalGross($total) : $this->setTotalNet($total);
    }

    public function getSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getSubtotalGross() : $this->getSubtotalNet();
    }

    public function setSubtotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setSubtotalGross($total) : $this->setSubtotalNet($total);
    }

    public function getTotalTax(): int
    {
        if (!$this->getTaxes() instanceof Fieldcollection) {
            return 0;
        }

        $totalTax = 0;

        foreach ($this->getTaxes()->getItems() as $taxItem) {
            if (!$taxItem instanceof TaxItemInterface) {
                continue;
            }

            $totalTax += $taxItem->getAmount();
        }

        return $totalTax;
    }

    public function getConvertedTotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedTotalGross() : $this->getConvertedTotalNet();
    }

    public function setConvertedTotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setConvertedTotalGross($total) : $this->setConvertedTotalNet($total);
    }

    public function getConvertedSubtotal(bool $withTax = true): int
    {
        return $withTax ? $this->getConvertedSubtotalGross() : $this->getConvertedSubtotalNet();
    }

    public function setConvertedSubtotal(int $total, bool $withTax = true)
    {
        $withTax ? $this->setConvertedSubtotalGross($total) : $this->setConvertedSubtotalNet($total);
    }

    public function getConvertedTotalTax(): int
    {
        if (!$this->getConvertedTaxes() instanceof Fieldcollection) {
            return 0;
        }

        $totalTax = 0;

        foreach ($this->getConvertedTaxes()->getItems() as $taxItem) {
            if (!$taxItem instanceof TaxItemInterface) {
                continue;
            }

            $totalTax += $taxItem->getAmount();
        }

        return $totalTax;
    }

    public function getTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTotalNet(int $totalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTotalGross(int $totalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setSubtotalNet(int $totalNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setSubtotalGross(int $totalGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTaxes(?Fieldcollection $taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedSubtotalNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedSubtotalNet(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedSubtotalGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedSubtotalGross(int $total)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getConvertedTaxes()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setConvertedTaxes(?Fieldcollection $taxes)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    protected function recalculateAfterAdjustmentChange(): void
    {
        $this->setTotal($this->getSubtotal(true) + $this->getAdjustmentsTotal(null, true), true);
        $this->setTotal($this->getSubtotal(false) + $this->getAdjustmentsTotal(null, false), false);
    }

    protected function recalculateConvertedAfterAdjustmentChange(): void
    {
        $this->setConvertedTotal($this->getConvertedSubtotal(true) + $this->getConvertedAdjustmentsTotal(null, true), true);
        $this->setConvertedTotal($this->getConvertedSubtotal(false) + $this->getConvertedAdjustmentsTotal(null, false), false);
    }
}
