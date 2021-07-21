<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Repository\ProductQuantityPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductQuantityPriceRuleContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ProductQuantityPriceRuleRepositoryInterface $productQuantityPriceRuleRepository;

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
    public function getProductQuantityPriceRuleByProductAndName($ruleName): ProductQuantityPriceRuleInterface
    {
        $rule = $this->productQuantityPriceRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, ProductQuantityPriceRuleInterface::class);

        return $rule;
    }

    /**
     * @Transform /^(quantity price rule)$/
     */
    public function getLatestSpecificProductQuantityPriceRule(): ProductQuantityPriceRuleInterface
    {
        $resource = $this->sharedStorage->get('product-quantity-price-rule');

        Assert::isInstanceOf($resource, ProductQuantityPriceRuleInterface::class);

        return $resource;
    }

    /**
     * @Transform /^(price range)$/
     */
    public function getPriceRange(): QuantityRangeInterface
    {
        $resource = $this->sharedStorage->get('quantity-price-rule-range');

        Assert::isInstanceOf($resource, QuantityRangeInterface::class);

        return $resource;
    }
}
