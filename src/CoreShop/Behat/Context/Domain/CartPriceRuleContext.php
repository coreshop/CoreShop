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
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use Webmozart\Assert\Assert;

final class CartPriceRuleContext implements Context
{
    private $sharedStorage;
    private $shopperContext;
    private $cartPriceRuleValidationProcessor;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShopperContextInterface $shopperContext,
        CartPriceRuleValidationProcessorInterface $cartPriceRuleValidationProcessor
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shopperContext = $shopperContext;
        $this->cartPriceRuleValidationProcessor = $cartPriceRuleValidationProcessor;
    }

    /**
     * @Then /^the (cart rule "[^"]+") should be valid for (my cart)$/
     * @Then /^the (cart rule) should be valid for (my cart)$/
     */
    public function theSpecificPriceRuleForProductShouldBeValid(CartPriceRuleInterface $cartPriceRule, OrderInterface $cart)
    {
        Assert::true($this->cartPriceRuleValidationProcessor->isValidCartRule($cart, $cartPriceRule));
    }

    /**
     * @Then /^the (cart rule "[^"]+") should be invalid for (my cart)$/
     * @Then /^the (cart rule) should be invalid for (my cart)$/
     */
    public function theSpecificPriceRuleForProductShouldBeInvalid(CartPriceRuleInterface $cartPriceRule, OrderInterface $cart)
    {
        Assert::false($this->cartPriceRuleValidationProcessor->isValidCartRule($cart, $cartPriceRule));
    }
}
