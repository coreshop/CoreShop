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

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\TaxationBundle\Doctrine\ORM\TaxRuleRepository as BaseTaxRuleRepository;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Core\Repository\TaxRuleRepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

class TaxRuleRepository extends BaseTaxRuleRepository implements TaxRuleRepositoryInterface
{
    public function findForCountryAndState(
        TaxRuleGroupInterface $taxRuleGroup,
        CountryInterface $country = null,
        StateInterface $state = null
    ): array {
        return $this->createQueryBuilder('o')
            ->andWhere('o.taxRuleGroup = :taxRuleGroup')
            ->andWhere('(o.country = :country OR o.country IS NULL)')
            ->andWhere('(o.state  = :state OR o.state IS NULL)')
            ->setParameter('taxRuleGroup', $taxRuleGroup->getId())
            ->setParameter('country', $country ? $country->getId() : 0)
            ->setParameter('state', $state ? $state->getId() : 0)
            ->getQuery()
            ->getResult();
    }
}
