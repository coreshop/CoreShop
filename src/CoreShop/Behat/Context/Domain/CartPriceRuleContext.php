<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use Webmozart\Assert\Assert;

final class CartPriceRuleContext implements Context
{
    public function __construct(
        private CartPriceRuleValidationProcessorInterface $cartPriceRuleValidationProcessor,
    ) {
    }

    /**
     * @Then /^the (cart rule "[^"]+") should be valid for (my cart)$/
     * @Then /^the (cart rule) should be valid for (my cart)$/
     */
    public function theSpecificPriceRuleForProductShouldBeValid(CartPriceRuleInterface $cartPriceRule, OrderInterface $cart): void
    {
        Assert::true($this->cartPriceRuleValidationProcessor->isValidCartRule($cart, $cartPriceRule));
    }

    /**
     * @Then /^the (cart rule "[^"]+") should be invalid for (my cart)$/
     * @Then /^the (cart rule) should be invalid for (my cart)$/
     */
    public function theSpecificPriceRuleForProductShouldBeInvalid(CartPriceRuleInterface $cartPriceRule, OrderInterface $cart): void
    {
        Assert::false($this->cartPriceRuleValidationProcessor->isValidCartRule($cart, $cartPriceRule));
    }
}
