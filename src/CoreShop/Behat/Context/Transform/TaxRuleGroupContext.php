<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use Webmozart\Assert\Assert;

final class TaxRuleGroupContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private RepositoryInterface $taxRuleGroupRepository)
    {
    }

    /**
     * @Transform /^tax rule group "([^"]+)"$/
     */
    public function getTaxRuleGroupByName($name): TaxRuleGroupInterface
    {
        /**
         * @var TaxRuleGroupInterface[] $groups
         */
        $groups = $this->taxRuleGroupRepository->findBy(['name' => $name]);

        Assert::eq(
            count($groups),
            1,
            sprintf('%d tax rule groups has been found with name "%s".', count($groups), $name)
        );

        return reset($groups);
    }

    /**
     * @Transform /^tax rule group$/
     */
    public function theTaxRuleGroup(): TaxRuleGroupInterface
    {
        return $this->sharedStorage->get('taxRuleGroup');
    }
}
