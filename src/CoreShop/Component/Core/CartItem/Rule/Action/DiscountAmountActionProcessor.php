<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\CartItem\Rule\Action;

use CoreShop\Component\Core\CartItem\Rule\Applier\CartItemRuleApplierInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\CartItem\Rule\Action\CartItemPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use Webmozart\Assert\Assert;

class DiscountAmountActionProcessor implements CartItemPriceRuleActionProcessorInterface
{
    public function __construct(
        protected CurrencyConverterInterface $moneyConverter,
        protected CurrencyRepositoryInterface $currencyRepository,
        protected CartItemRuleApplierInterface $cartItemRuleApplier
    )
    {
    }

    public function applyRule(
        OrderItemInterface $orderItem,
        array $configuration,
        PriceRuleItemInterface $cartPriceRuleItem
    ): bool {
        $discount = $this->getDiscount($orderItem, $configuration);

        if ($discount <= 0) {
            return false;
        }

        $this->cartItemRuleApplier->applyDiscount($orderItem, $cartPriceRuleItem, $discount, $configuration['gross']);

        return true;
    }

    public function unApplyRule(
        OrderItemInterface $orderItem,
        array $configuration,
        PriceRuleItemInterface $cartPriceRuleItem
    ): bool {
        return true;
    }

    protected function getDiscount(OrderItemInterface $orderItem, array $configuration): int
    {
        $applyOn = $configuration['applyOn'] ?? 'total';

        if ('total' === $applyOn) {
            $cartAmount = $orderItem->getTotal($configuration['gross']);
        } else {
            $cartAmount =
                $orderItem->getSubtotal($configuration['gross']) +
                $orderItem->getAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, $configuration['gross']);
        }

        $currency = $this->currencyRepository->find($configuration['currency']);
        $amount = $configuration['amount'];

        Assert::isInstanceOf($currency, CurrencyInterface::class);

        return $this->moneyConverter->convert(
            $this->getApplicableAmount($cartAmount, $amount),
            $currency->getIsoCode(),
            $orderItem->getOrder()->getCurrency()->getIsoCode()
        );
    }

    protected function getApplicableAmount(int $cartAmount, int $ruleAmount): int
    {
        return min($cartAmount, $ruleAmount);
    }
}
