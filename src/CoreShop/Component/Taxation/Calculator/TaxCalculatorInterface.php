<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Taxation\Calculator;

use CoreShop\Component\Taxation\Model\TaxRateInterface;

interface TaxCalculatorInterface
{
    /**
     * DISABLE_METHOD only use this tax.
     */
    public const DISABLE_METHOD = 0;

    /**
     * COMBINE_METHOD sum taxes
     * eg: 100€ * (10% + 15%).
     */
    public const COMBINE_METHOD = 1;

    /**
     * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
     * eg: (100€ * 10%) * 15%.
     */
    public const ONE_AFTER_ANOTHER_METHOD = 2;

    /**
     * Compute and add the taxes to the specified price.
     */
    public function applyTaxes(int $price): int;

    /**
     * Compute and remove the taxes to the specified price.
     */
    public function removeTaxes(int $price): int;

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     *
     *
     * @return int $taxes_amount
     */
    public function getTaxesAmountFromGross(int $price): int;

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     *
     *
     * @return int[] $taxes_amount
     */
    public function getTaxesAmountFromGrossAsArray(int $price): array;

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     */
    public function getTaxesAmount(int $price): int;

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     */
    public function getTaxesAmountAsArray(int $price): array;

    /**
     * Compute Total Rate.
     */
    public function getTotalRate(): float;

    /**
     * @return TaxRateInterface[]
     */
    public function getTaxRates(): array;
}
