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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductPriceRuleContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ProductPriceRuleRepositoryInterface $productPriceRuleRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductPriceRuleRepositoryInterface $productPriceRuleRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productPriceRuleRepository = $productPriceRuleRepository;
    }

    /**
     * @Transform /^price rule "([^"]+)"$/
     */
    public function getPriceRuleByProductAndName($ruleName)
    {
        $rule = $this->productPriceRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, ProductPriceRuleInterface::class);

        return $rule;
    }

    /**
     * @Transform /^(price rule)$/
     */
    public function getLatestPriceRule()
    {
        $resource = $this->sharedStorage->getLatestResource();

        Assert::isInstanceOf($resource, ProductPriceRuleInterface::class);

        return $resource;
    }
}
