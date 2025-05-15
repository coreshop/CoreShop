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

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Webmozart\Assert\Assert;

final class TaxApplicator implements TaxApplicatorInterface
{
    public function applyTax(
        int $price,
        array $context,
        TaxCalculatorInterface $taxCalculator,
        bool $withTax = true,
    ): int {
        Assert::keyExists($context, 'store');
        Assert::isInstanceOf($context['store'], StoreInterface::class);

        $useGrossPrice = $context['store']->getUseGrossPrice();

        if ($useGrossPrice) {
            if ($withTax) {
                return $price;
            }

            return $taxCalculator->removeTaxes($price);
        }

        if ($withTax) {
            return $taxCalculator->applyTaxes($price);
        }

        return $price;
    }
}
