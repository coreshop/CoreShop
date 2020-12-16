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
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

abstract class Adjustment extends AbstractPimcoreFieldcollection implements AdjustmentInterface
{
    public function getId()
    {
        return $this->getObject()->getId() . '_tax_item_' . $this->getIndex();
    }

    public function getAdjustable(): ?AdjustableInterface
    {
        $object = $this->getObject();

        if ($object instanceof AdjustableInterface) {
            return $object;
        }

        return null;
    }

    public function getAmount(bool $withTax = true): int
    {
        return $withTax ? $this->getPimcoreAmountGross() : $this->getPimcoreAmountNet();
    }

    public function setAmount(int $grossAmount, int $netAmount)
    {
        $this->setPimcoreAmountGross($grossAmount);
        $this->setPimcoreAmountNet($netAmount);

        if (!$this->getNeutral()) {
            $this->recalculateAdjustable();
        }
    }

    public function getNeutral(): bool
    {
        return (bool)$this->getPimcoreNeutral();
    }

    public function setNeutral(bool $neutral)
    {
        if ($this->getPimcoreNeutral() !== $neutral) {
            $this->setPimcoreNeutral($neutral);

            $this->recalculateAdjustable();
        }
    }

    public function isCharge(): bool
    {
        return 0 > $this->getAmount();
    }

    public function isCredit(): bool
    {
        return 0 < $this->getAmount();
    }

    public function getTypeIdentifier(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setTypeIdentifier(?string $typeIdentifier)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getLabel(): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setLabel(?string $label)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setPimcoreAmountNet(int $pimcoreAmountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getPimcoreAmountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setPimcoreAmountGross(int $pimcoreAmountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getPimcoreAmountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setPimcoreNeutral(?bool $pimcoreNeutral)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getPimcoreNeutral(): ?bool
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    private function recalculateAdjustable(): void
    {
        $adjustable = $this->getAdjustable();
        if (null !== $adjustable) {
            $adjustable->recalculateAdjustmentsTotal();
        }
    }
}
