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

namespace CoreShop\Component\Order\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

abstract class AbstractConditionChecker implements CartRuleConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, $params = [])
    {
        Assert::isInstanceOf($subject, CartInterface::class);
        Assert::keyExists($params, 'cartPriceRule');
        Assert::keyExists($params, 'voucher');
        Assert::nullOrIsInstanceOf($params['voucher'], CartPriceRuleVoucherCodeInterface::class);
        Assert::isInstanceOf($params['cartPriceRule'], CartPriceRuleInterface::class);

        return $this->isCartRuleValid($subject, $params['cartPriceRule'], $params['voucher'], $configuration);
    }
}
