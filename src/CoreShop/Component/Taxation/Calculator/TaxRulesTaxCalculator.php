<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Taxation\Calculator;

use CoreShop\Component\Taxation\Model\TaxRateInterface;

class TaxRulesTaxCalculator implements TaxCalculatorInterface
{
    /**
     * @var TaxRateInterface[]
     */
    public array $taxRates;
    public int $computationMethod;

    public function __construct(array $taxRates = [], $computationMethod = self::COMBINE_METHOD)
    {
        $this->taxRates = $taxRates;
        $this->computationMethod = (int) $computationMethod;
    }

    public function applyTaxes(int $price): int
    {
        return (int) round($price * (1 + ($this->getTotalRate() / 100)));
    }

    public function removeTaxes(int $price): int
    {
        return (int) round($price / (1 + $this->getTotalRate() / 100));
    }

    public function getTotalRate(): float
    {
        $taxes = 0;
        if ($this->computationMethod === self::ONE_AFTER_ANOTHER_METHOD) {
            $taxes = 1;
            foreach ($this->getTaxRates() as $tax) {
                $taxes *= (1 + (abs($tax->getRate()) / 100));
            }

            --$taxes;
            $taxes *= 100;
        } else {
            foreach ($this->getTaxRates() as $tax) {
                $taxes += abs($tax->getRate());
            }
        }

        return (float) $taxes;
    }

    public function getTaxesAmountFromGross(int $price): int
    {
        return array_sum($this->getTaxesAmountFromGrossAsArray($price));
    }

    public function getTaxesAmountFromGrossAsArray(int $price): array
    {
        $taxesAmounts = [];

        foreach ($this->getTaxRates() as $tax) {
            if ($this->computationMethod === self::ONE_AFTER_ANOTHER_METHOD) {
                $taxesAmounts[$tax->getId()] = (int) round($price - ($price / (1 + ($tax->getRate() / 100))));
                $price -= $taxesAmounts[$tax->getId()];
            } else {
                $taxesAmounts[$tax->getId()] = (int) round($price - ($price / (1 + ($tax->getRate() / 100))));
            }
        }

        return $taxesAmounts;
    }

    public function getTaxesAmount(int $price): int
    {
        return (int)array_sum($this->getTaxesAmountAsArray($price));
    }

    public function getTaxesAmountAsArray(int $price): array
    {
        $taxesAmounts = [];

        foreach ($this->getTaxRates() as $tax) {
            if ($this->computationMethod === self::ONE_AFTER_ANOTHER_METHOD) {
                $taxesAmounts[$tax->getId()] = (int) round($price * (abs($tax->getRate()) / 100));
                $price += $taxesAmounts[$tax->getId()];
            } else {
                $taxesAmounts[$tax->getId()] = (int) round(($price * (abs($tax->getRate()) / 100)));
            }
        }

        return $taxesAmounts;
    }


    public function getTaxRates(): array
    {
        return $this->taxRates;
    }
}
