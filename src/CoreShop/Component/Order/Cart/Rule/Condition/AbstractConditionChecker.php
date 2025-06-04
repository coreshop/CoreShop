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

namespace CoreShop\Component\Order\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

abstract class AbstractConditionChecker implements CartRuleConditionCheckerInterface
{
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, array $params = []): bool
    {
        Assert::isInstanceOf($subject, OrderInterface::class);
        Assert::keyExists($params, 'cartPriceRule');
        Assert::keyExists($params, 'voucher');
        Assert::nullOrIsInstanceOf($params['voucher'], CartPriceRuleVoucherCodeInterface::class);
        Assert::isInstanceOf($params['cartPriceRule'], CartPriceRuleInterface::class);

        return $this->isCartRuleValid($subject, $params['cartPriceRule'], $params['voucher'], $configuration);
    }
}
