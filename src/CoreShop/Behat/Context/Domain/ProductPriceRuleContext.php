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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductPriceRuleInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use Webmozart\Assert\Assert;

final class ProductPriceRuleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var RuleValidationProcessorInterface
     */
    private $ruleValidationProcessor;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ShopperContextInterface $shopperContext
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShopperContextInterface $shopperContext,
        RuleValidationProcessorInterface $ruleValidationProcessor
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->shopperContext = $shopperContext;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
    }

    /**
     * @Then /^the (price rule "[^"]+") for (product "[^"]+") should be valid$/
     * @Then /^the (price rule) should be valid for (product "[^"]+")$/
     */
    public function theSpecificPriceRuleForProductShouldBeValid(ProductPriceRuleInterface $productPriceRule, ProductInterface $product)
    {
        Assert::true($this->ruleValidationProcessor->isValid($product, $productPriceRule, $this->getContext()));
    }

    /**
     * @Then /^the (price rule "[^"]+") for (product "[^"]+") should be invalid$/
     * @Then /^the (price rule) should be invalid for (product "[^"]+")$/
     */
    public function theSpecificPriceRuleForProductShouldBeInvalid(ProductPriceRuleInterface $productPriceRule, ProductInterface $product)
    {
        Assert::false($this->ruleValidationProcessor->isValid($product, $productPriceRule, $this->getContext()));
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        return [
            'store' => $this->shopperContext->getStore(),
            'customer' => $this->shopperContext->hasCustomer() ? $this->shopperContext->getCustomer() : null,
            'currency' => $this->shopperContext->getCurrency(),
            'country' => $this->shopperContext->getCountry(),
            'cart' => $this->shopperContext->getCart()
        ];
    }
}
