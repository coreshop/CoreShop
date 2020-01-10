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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Repository\ProductQuantityPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductQuantityPriceRuleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductQuantityPriceRuleRepositoryInterface
     */
    private $productQuantityPriceRuleRepository;

    /**
     * @param SharedStorageInterface                      $sharedStorage
     * @param ProductQuantityPriceRuleRepositoryInterface $productQuantityPriceRuleRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductQuantityPriceRuleRepositoryInterface $productQuantityPriceRuleRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productQuantityPriceRuleRepository = $productQuantityPriceRuleRepository;
    }

    /**
     * @Transform /^quantity price rule "([^"]+)"$/
     */
    public function getProductQuantityPriceRuleByProductAndName($ruleName)
    {
        $rule = $this->productQuantityPriceRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, ProductQuantityPriceRuleInterface::class);

        return $rule;
    }

    /**
     * @Transform /^(quantity price rule)$/
     */
    public function getLatestSpecificProductQuantityPriceRule()
    {
        $resource = $this->sharedStorage->getLatestResource();

        Assert::isInstanceOf($resource, ProductQuantityPriceRuleInterface::class);

        return $resource;
    }
}
