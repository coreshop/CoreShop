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

namespace CoreShop\Component\Taxation\Collector;

use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;

interface TaxCollectorInterface
{
    /**
     * @return TaxItemInterface[]
     */
    public function collectTaxes(TaxCalculatorInterface $taxCalculator, int $price, array $usedTaxes = []): array;

    /**
     * @return TaxItemInterface[]
     */
    public function collectTaxesFromGross(TaxCalculatorInterface $taxCalculator, int $price, array $usedTaxes = []): array;

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
