<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Taxation\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Model\TaxRuleInterface;

interface TaxRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $groupId
     * @return TaxRuleInterface[]
     *
     * @deprecated getByGroupId is deprecated since 2.0.0 and will be removed in 2.1.0. Please use findByGroup instead
     */
    public function getByGroupId($groupId);

    /**
     * @param TaxRuleGroupInterface $group
     * @return TaxRuleInterface[]
     */
    public function findByGroup(TaxRuleGroupInterface $group);
}
