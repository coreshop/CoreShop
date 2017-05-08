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
 *
*/

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface TaxCollectorInterface {
    /**
     * @param TaxCalculatorInterface $taxCalculator
     * @param $price
     * @param array $usedTaxes
     * @return mixed
     */
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, $price, $usedTaxes = []);

    /**
     * Merges to Tax arrays from TaxCollector into one
     *
     * @param $taxes1
     * @param $taxes2
     * @return mixed
     */
    public function mergeTaxes($taxes1, $taxes2);
}