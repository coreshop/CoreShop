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

declare(strict_types=1);

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use Webmozart\Assert\Assert;

final class TaxApplicator implements TaxApplicatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyTax(
        int $price,
        array $context,
        TaxCalculatorInterface $taxCalculator,
        bool $withTax = true
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
