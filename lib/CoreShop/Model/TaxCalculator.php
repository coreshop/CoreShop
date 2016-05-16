<?php
/**
 * CoreShop.
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
     * DISABLE_METHOD only use this tax.
     */
    const DISABLE_METHOD = 0;

    /**
     * COMBINE_METHOD sum taxes
     * eg: 100€ * (10% + 15%).
     */
    const COMBINE_METHOD = 1;

    /**
     * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
     * eg: (100€ * 10%) * 15%.
     */
    const ONE_AFTER_ANOTHER_METHOD = 2;

    /**
     * @var array
     */
    public $taxes;

    /**
     * @var int (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public $computation_method;

    /**
     * Constructor.
     *
     * @param array $taxes
     * @param int   $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public function __construct(array $taxes = array(), $computation_method = self::COMBINE_METHOD)
    {
        $this->taxes = $taxes;
        $this->computation_method = (int) $computation_method;
    }

    /**
     * Compute and add the taxes to the specified price.
     *
     * @param float $price price tax excluded
     *
     * @return float price with taxes
     */
    public function addTaxes($price)
    {
        return $price * (1 + ($this->getTotalRate() / 100));
    }

    /**
     * Compute and remove the taxes to the specified price.
     *
     * @param float $price price tax inclusive
     *
     * @return float price without taxes
     */
    public function removeTaxes($price)
    {
        return $price / (1 + $this->getTotalRate() / 100);
    }

    /**
     * get Total Rate.
     *
     * @return float total taxes rate
     */
    public function getTotalRate()
    {
        $taxes = 0;
        if ($this->getComputationMethod() == self::ONE_AFTER_ANOTHER_METHOD) {
            $taxes = 1;
            foreach ($this->getTaxes() as $tax) {
                $taxes *= (1 + (abs($tax->getRate()) / 100));
            }

            $taxes = $taxes - 1;
            $taxes = $taxes * 100;
        } else {
            foreach ($this->getTaxes() as $tax) {
                $taxes += abs($tax->getRate());
            }
        }

        return (float) $taxes;
    }

    /**
     * get Tax Names.
     *
     * @param null $language
     *
     * @return string
     */
    public function getTaxesName($language = null)
    {
        $name = '';
        foreach ($this->getTaxes() as $tax) {
            $name .= $tax->getName($language).' - ';
        }

        $name = rtrim($name, ' - ');

        return $name;
    }

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     *
     * @param float $price
     * @param bool  $asArray
     *
     * @return array $taxes_amount
     */
    public function getTaxesAmount($price, $asArray = false)
    {
        $taxes_amounts = array();
        $taxAmount = 0;

        foreach ($this->getTaxes() as $tax) {
            if ($this->getComputationMethod() == self::ONE_AFTER_ANOTHER_METHOD) {
                $taxes_amounts[$tax->id] = $price * (abs($tax->getRate()) / 100);
                $price = $price + $taxes_amounts[$tax->id];
            } else {
                $taxes_amounts[$tax->id] = ($price * (abs($tax->getRate()) / 100));
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
     * Get taxes.
     *
     * @return Tax[]
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     *  return computation mode.
     */
    public function getComputationMethod()
    {
        return $this->computation_method;
    }
}
