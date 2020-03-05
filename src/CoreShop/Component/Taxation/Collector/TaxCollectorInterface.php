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

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;

interface TaxCollectorInterface
{
    /**
     * @param TaxCalculatorInterface $taxCalculator
     * @param int                    $price
     * @param array                  $usedTaxes
     *
     * @return TaxItemInterface[]
     */
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, $price, array $usedTaxes = []): array;

    /**
     * @param TaxCalculatorInterface $taxCalculator
     * @param int                    $price
     * @param array                  $usedTaxes
     *
     * @return TaxItemInterface[]
     */
    public function collectTaxesFromGross(TaxCalculatorInterface $taxCalculator, $price, array $usedTaxes = []): array;

    /**
     * Merges to Tax arrays from TaxCollector into one.
     *
     * @param TaxItemInterface[] $taxes1
     * @param TaxItemInterface[] $taxes2
     *
     * @return TaxItemInterface[]
     */
    public function mergeTaxes(array $taxes1, array $taxes2): array;
}
