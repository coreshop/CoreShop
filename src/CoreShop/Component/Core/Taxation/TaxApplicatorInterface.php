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

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface TaxApplicatorInterface
{
    /**
     * apply taxes on a price.
     *
     * @param int                    $price
     * @param array                  $context
     * @param TaxCalculatorInterface $taxCalculator
     * @param bool                   $withTax
     *
     * @return mixed
     */
    public function applyTax($price, array $context, TaxCalculatorInterface $taxCalculator, $withTax = true);
}
