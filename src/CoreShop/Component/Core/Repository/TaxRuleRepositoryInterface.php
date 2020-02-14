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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Taxation\Repository\TaxRuleRepositoryInterface as BaseTaxRuleRepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

interface TaxRuleRepositoryInterface extends BaseTaxRuleRepositoryInterface
{
    /**
     * @param TaxRuleGroupInterface $taxRuleGroup
     * @param CountryInterface|null $country
     * @param StateInterface|null   $state
     *
     * @return TaxRuleInterface[]
     */
    public function findForCountryAndState(TaxRuleGroupInterface $taxRuleGroup, CountryInterface $country = null, StateInterface $state = null): array;
}
