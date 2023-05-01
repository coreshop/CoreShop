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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Cart\Rule\Action;

use CoreShop\Component\Order\CartItem\Rule\Action\CartItemPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use Webmozart\Assert\Assert;

class CartItemActionProcessor implements CartPriceRuleActionProcessorInterface
{
    public function __construct(
        protected RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor,
        protected ServiceRegistryInterface $actionServiceRegistry,
        protected CartPriceRuleVoucherRepositoryInterface $voucherRepository,
        protected FactoryInterface $cartPriceRuleItemFactory,
        protected AdjustmentFactoryInterface $adjustmentFactory,
    ) {
    }

    public function applyRule(OrderInterface $cart, array $configuration, PriceRuleItemInterface $cartPriceRuleItem): bool
    {
        $overallResult = false;
        $totalDiscountGross = 0;
        $totalDiscountNet = 0;

        foreach ($cart->getItems() as $item) {
            $voucher = $cartPriceRuleItem->getVoucherCode() ? $this->voucherRepository->findByCode($cartPriceRuleItem->getVoucherCode()) : null;
            $priceRuleItem = $item->getPriceRuleByCartPriceRule($cartPriceRuleItem->getCartPriceRule(), $voucher);

            $params = [
                'voucher' => $voucher,
                'cartPriceRule' => $cartPriceRuleItem->getCartPriceRule(),
            ];

            $existingPriceRule = null !== $priceRuleItem;
            $result = false;

            if ($priceRuleItem === null) {
                /**
                 * @var PriceRuleItemInterface $priceRuleItem
                 */
                $priceRuleItem = $this->cartPriceRuleItemFactory->createNew();
            }

            $priceRuleItem->setCartPriceRule($cartPriceRuleItem->getCartPriceRule());
            $priceRuleItem->setVoucherCode($cartPriceRuleItem->getVoucherCode());
            $priceRuleItem->setDiscount(0, true);
            $priceRuleItem->setDiscount(0, false);

            if (!$this->isValid($item, $cartPriceRuleItem, $configuration['conditions'], $params)) {
                $item->removePriceRule($priceRuleItem);

                continue;
            }

            foreach ($configuration['actions'] as $action) {
                $actionCommand = $this->actionServiceRegistry->get($action->getType());

                /**
                 * @var CartItemPriceRuleActionProcessorInterface $actionCommand
                 */
                Assert::isInstanceOf($actionCommand, CartItemPriceRuleActionProcessorInterface::class);

                $config = $action->getConfiguration();
                $config['action'] = $action;

                $actionResult = $actionCommand->applyRule($item, $config, $priceRuleItem);
                $result = $result || $actionResult;
            }

            if (!$result) {
                $item->removePriceRule($priceRuleItem);
            } elseif (!$existingPriceRule) {
                $item->addPriceRule($priceRuleItem);
            }

            $overallResult = $result || $overallResult;

            if ($result) {
                $totalDiscountGross += $priceRuleItem->getDiscount(true);
                $totalDiscountNet += $priceRuleItem->getDiscount(false);
            }
        }

        $cartPriceRuleItem->setDiscount($totalDiscountNet, false);
        $cartPriceRuleItem->setDiscount($totalDiscountGross, true);

        $cart->addAdjustment(
            $this->adjustmentFactory->createWithData(
                AdjustmentInterface::CART_PRICE_RULE,
                $cartPriceRuleItem->getCartPriceRule()->getName(),
                $cartPriceRuleItem->getDiscount(true),
                $cartPriceRuleItem->getDiscount(false),
            ),
        );

        return $overallResult;
    }

    public function unApplyRule(OrderInterface $cart, array $configuration, PriceRuleItemInterface $cartPriceRuleItem): bool
    {
        foreach ($cart->getItems() as $item) {
            $voucher = $cartPriceRuleItem->getVoucherCode() ? $this->voucherRepository->findByCode($cartPriceRuleItem->getVoucherCode()) : null;
            $priceRuleItem = $item->getPriceRuleByCartPriceRule($cartPriceRuleItem->getCartPriceRule(), $voucher);

            if (!$priceRuleItem instanceof PriceRuleItemInterface) {
                continue;
            }

            foreach ($configuration['actions'] as $action) {
                $actionCommand = $this->actionServiceRegistry->get($action->getType());

                /**
                 * @var CartItemPriceRuleActionProcessorInterface $actionCommand
                 */
                Assert::isInstanceOf($actionCommand, CartItemPriceRuleActionProcessorInterface::class);

                $config = $action->getConfiguration();
                $config['action'] = $action;

                $actionCommand->unApplyRule($item, $config, $priceRuleItem);
            }

            $item->removePriceRule($priceRuleItem);
        }

        return true;
    }

    public function isValid(OrderItemInterface $subject, PriceRuleItemInterface $cartPriceRuleItem, array $conditions, array $params): bool
    {
        foreach ($conditions as $condition) {
            $conditionValid = $this->ruleConditionsValidationProcessor->isValid($subject, $cartPriceRuleItem->getCartPriceRule(), [$condition], $params);

            if (!$conditionValid) {
                return false;
            }
        }

        return true;
    }
}
