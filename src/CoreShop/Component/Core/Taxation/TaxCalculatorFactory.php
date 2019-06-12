<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Repository\TaxRuleRepositoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Calculator\TaxRulesTaxCalculator;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

class TaxCalculatorFactory implements TaxCalculatorFactoryInterface
{
    /**
     * @var TaxRuleRepositoryInterface
     */
    private $taxRuleRepository;

    /**
     * @var int
     */
    private $decimalFactor;

    /**
     * @param TaxRuleRepositoryInterface $taxRuleRepository
     * @param int                        $decimalFactor
     */
    public function __construct(
        TaxRuleRepositoryInterface $taxRuleRepository,
        int $decimalFactor
    ) {
        $this->taxRuleRepository = $taxRuleRepository;
        $this->decimalFactor = $decimalFactor;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCalculatorForAddress(TaxRuleGroupInterface $taxRuleGroup, AddressInterface $address)
    {
        $taxRules = $this->taxRuleRepository->findForCountryAndState($taxRuleGroup, $address->getCountry(), $address->getState());
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
