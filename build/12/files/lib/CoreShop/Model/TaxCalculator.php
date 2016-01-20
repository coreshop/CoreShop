<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

class TaxCalculator
{
    /**
     * DISABLE_METHOD only use this tax
     */
    const DISABLE_METHOD = 0;

    /**
     * COMBINE_METHOD sum taxes
     * eg: 100€ * (10% + 15%)
     */
    const COMBINE_METHOD = 1;

    /**
     * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
     * eg: (100€ * 10%) * 15%
     */
    const ONE_AFTER_ANOTHER_METHOD = 2;

    /**
     * @var array $taxes
     */
    public $taxes;

    /**
     * @var int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public $computation_method;


    /**
     * @param array $taxes
     * @param int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public function __construct(array $taxes = array(), $computation_method = TaxCalculator::COMBINE_METHOD)
    {
        $this->taxes = $taxes;
        $this->computation_method = (int)$computation_method;
    }

    /**
     * Compute and add the taxes to the specified price
     *
     * @param float $price price tax excluded
     * @return float price with taxes
     */
    public function addTaxes($price)
    {
        return $price * (1 + ($this->getTotalRate() / 100));
    }


    /**
     * Compute and remove the taxes to the specified price
     *
     * @param float $price price tax inclusive
     * @return float price without taxes
     */
    public function removeTaxes($price)
    {
        return $price / (1 + $this->getTotalRate() / 100);
    }

    /**
     * @return float total taxes rate
     */
    public function getTotalRate()
    {
        $taxes = 0;
        if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD)
        {
            $taxes = 1;
            foreach ($this->taxes as $tax)
                $taxes *= (1 + (abs($tax->getRate()) / 100));

            $taxes = $taxes - 1;
            $taxes = $taxes * 100;
        }
        else
        {
            foreach ($this->taxes as $tax)
                $taxes += abs($tax->getRate());
        }

        return (float)$taxes;
    }

    public function getTaxesName($language = null)
    {
        $name = '';
        foreach ($this->taxes as $tax)
            $name .= $tax->getName($language) . ' - ';

        $name = rtrim($name, ' - ');

        return $name;
    }

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator
     *
     * @param float $price
     * @return array $taxes_amount
     */
    public function getTaxesAmount($price)
    {
        $taxes_amounts = array();
        $taxAmount = 0;

        foreach ($this->taxes as $tax)
        {
            if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD)
            {
                $taxes_amounts += $price * (abs($tax->getRate()) / 100);
                $price = $price + $taxes_amounts[$tax->id];
            }
            else
                $taxes_amounts[$tax->id] = ($price * (abs($tax->getRate()) / 100));
        }

        foreach($taxes_amounts as $t) {
            $taxAmount += $t;
        }

        return $taxAmount;
    }
}

