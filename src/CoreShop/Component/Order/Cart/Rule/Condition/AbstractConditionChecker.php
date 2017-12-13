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

namespace CoreShop\Component\Order\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use Webmozart\Assert\Assert;

abstract class AbstractConditionChecker implements CartRuleConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        Assert::isArray($subject);
        Assert::keyExists($subject, 'cart');
        Assert::keyExists($subject, 'voucher');
        Assert::isInstanceOf($subject['cart'], CartInterface::class);
        Assert::isInstanceOf($subject['voucher'], CartPriceRuleVoucherCodeInterface::class);

        return $this->isCartRuleValid($subject['cart'], $subject['voucher'], $configuration);
    }
}
