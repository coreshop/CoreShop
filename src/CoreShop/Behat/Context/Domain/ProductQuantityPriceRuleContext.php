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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use Webmozart\Assert\Assert;

final class ProductQuantityPriceRuleContext implements Context
{
    private $sharedStorage;
    private $shopperContext;
    private $ruleValidationProcessor;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShopperContextInterface $shopperContext,
        RuleValidationProcessorInterface $ruleValidationProcessor
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shopperContext = $shopperContext;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
    }

    /**
     * @Then /^the (quantity price rule "[^"]+") for (product "[^"]+") should be valid$/
     * @Then /^the (quantity price rule) should be valid for (product "[^"]+")$/
     */
    public function theSpecificPriceRuleForProductShouldBeValid(ProductQuantityPriceRuleInterface $productSpecificPriceRule, ProductInterface $product)
    {
        Assert::true($this->ruleValidationProcessor->isValid($product, $productSpecificPriceRule, $this->shopperContext->getContext()));
    }

    /**
     * @Then /^the (quantity price rule "[^"]+") for (product "[^"]+") should be invalid$/
     * @Then /^the (quantity price rule) should be invalid for (product "[^"]+")$/
     */
    public function theSpecificPriceRuleForProductShouldBeInvalid(ProductQuantityPriceRuleInterface $productSpecificPriceRule, ProductInterface $product)
    {
        Assert::false($this->ruleValidationProcessor->isValid($product, $productSpecificPriceRule, $this->shopperContext->getContext()));
    }
}
