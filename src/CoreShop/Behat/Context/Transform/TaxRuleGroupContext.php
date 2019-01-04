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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRuleGroupContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $taxRuleGroupRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface    $taxRuleGroupRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, RepositoryInterface $taxRuleGroupRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->taxRuleGroupRepository = $taxRuleGroupRepository;
    }

    /**
     * @Transform /^tax rule group "([^"]+)"$/
     */
    public function getTaxRuleGroupByName($name)
    {
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
    public function theTaxRuleGroup()
    {
        return $this->sharedStorage->get('taxRuleGroup');
    }
}
