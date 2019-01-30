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
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Repository\ProductSpecificTierPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductSpecificTierPriceRuleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductSpecificTierPriceRuleRepositoryInterface
     */
    private $productSpecificTierPriceRuleRepository;

    /**
     * @param SharedStorageInterface                      $sharedStorage
     * @param ProductSpecificTierPriceRuleRepositoryInterface $productSpecificTierPriceRuleRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        productSpecificTierPriceRuleRepositoryInterface $productSpecificTierPriceRuleRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productSpecificTierPriceRuleRepository = $productSpecificTierPriceRuleRepository;
    }

    /**
     * @Transform /^specific tier price rule "([^"]+)"$/
     */
    public function getTierPriceRuleByProductAndName($ruleName)
    {
        $rule = $this->productSpecificTierPriceRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, ProductSpecificTierPriceRuleInterface::class);

        return $rule;
    }

    /**
     * @Transform /^(specific tier price rule)$/
     */
    public function getLatestSpecificTierPriceRule()
    {
        $resource = $this->sharedStorage->getLatestResource();

        Assert::isInstanceOf($resource, ProductSpecificTierPriceRuleInterface::class);

        return $resource;
    }
}
