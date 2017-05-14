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

namespace CoreShop\Component\Taxation\Calculator;

use CoreShop\Component\Taxation\Model\TaxRateInterface;

class TaxRulesTaxCalculator implements TaxCalculatorInterface
{
    /**
     * @var TaxRateInterface[]
     */
    public $taxRates;

    /**
     * @var int (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public $computation_method;

    /**
     * @param array $taxRates
     * @param int   $computation_method
     */
    public function __construct(array $taxRates = [], $computation_method = self::COMBINE_METHOD)
    {
        $this->taxRates = $taxRates;
        $this->computation_method = (int) $computation_method;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxes($price)
    {
        return $price * (1 + ($this->getTotalRate() / 100));
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxes($price)
    {
        return $price / (1 + $this->getTotalRate() / 100);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalRate()
    {
        $taxes = 0;
        if ($this->getComputationMethod() == self::ONE_AFTER_ANOTHER_METHOD) {
            $taxes = 1;
            foreach ($this->getTaxRates() as $tax) {
                $taxes *= (1 + (abs($tax->getRate()) / 100));
            }

            $taxes = $taxes - 1;
            $taxes = $taxes * 100;
        } else {
            foreach ($this->getTaxRates() as $tax) {
                $taxes += abs($tax->getRate());
            }
        }

        return (float) $taxes;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxesAmount($price, $asArray = false)
    {
        $taxes_amounts = [];
        $taxAmount = 0.0;

        foreach ($this->getTaxRates() as $tax) {
            if ($this->getComputationMethod() == self::ONE_AFTER_ANOTHER_METHOD) {
                $taxes_amounts[$tax->getId()] = $price * (abs($tax->getRate()) / 100);
                $price = $price + $taxes_amounts[$tax->getId()];
            } else {
                $taxes_amounts[$tax->getId()] = ($price * (abs($tax->getRate()) / 100));
            }
        }

        if ($asArray) {
            return $taxes_amounts;
        }

        foreach ($taxes_amounts as $t) {
            $taxAmount += $t;
        }

        return $taxAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRates()
    {
        return $this->taxRates;
    }

    /**
     *  return computation mode.
     */
    private function getComputationMethod()
    {
        return $this->computation_method;
    }
}
