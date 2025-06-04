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

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Repository\TaxRuleRepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Calculator\TaxRulesTaxCalculator;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

class TaxCalculatorFactory implements TaxCalculatorFactoryInterface
{
    public function __construct(
        private TaxRuleRepositoryInterface $taxRuleRepository,
    ) {
    }

    public function getTaxCalculatorForAddress(
        TaxRuleGroupInterface $taxRuleGroup,
        AddressInterface $address,
        array $context = [],
    ): TaxCalculatorInterface {
        $taxRules = $this->taxRuleRepository->findForCountryAndState(
            $taxRuleGroup,
            $address->getCountry(),
            $address->getState(),
        );
        $taxRates = [];
        $firstRow = true;
        $behavior = TaxRulesTaxCalculator::COMBINE_METHOD;

        foreach ($taxRules as $taxRule) {
            $taxRate = $taxRule->getTaxRate();

            $taxRates[] = $taxRate;

            if ($firstRow) {
                $behavior = $taxRule->getBehavior();

                $firstRow = false;
            }

            if ($taxRule->getBehavior() === TaxCalculatorInterface::DISABLE_METHOD) {
                break;
            }
        }

        return new TaxRulesTaxCalculator($taxRates, $behavior);
    }
}
