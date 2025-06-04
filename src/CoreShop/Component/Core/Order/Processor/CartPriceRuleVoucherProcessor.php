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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\ProposalCartPriceRuleCalculatorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartPriceRuleVoucherProcessor implements CartProcessorInterface
{
    public function __construct(
        private ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator,
        private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        private CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator,
        private CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        if ($cart->isImmutable()) {
            return;
        }

        $priceRuleItems = $cart->getPriceRuleItems();

        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $index => $item) {
            if (!$item instanceof PriceRuleItemInterface) {
                continue;
            }

            if (!$item->getCartPriceRule() instanceof CartPriceRuleInterface) {
                $priceRuleItems->remove($index);

                continue;
            }

            if ($item->getCartPriceRule()->getIsVoucherRule() && null === $item->getVoucherCode()) {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule());
            }

            if (!$item->getVoucherCode()) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($item->getVoucherCode());

            if (!$item->getCartPriceRule()->getIsVoucherRule() && null !== $item->getVoucherCode()) {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule(), $voucherCode);
            }

            if ($this->cartPriceRuleValidator->isValidCartRule($cart, $item->getCartPriceRule(), $voucherCode)) {
                $this->proposalCartPriceRuleCalculator->calculatePriceRule(
                    $cart,
                    $item->getCartPriceRule(),
                    $voucherCode,
                );
            } else {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule(), $voucherCode);
            }
        }
    }
}
