<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;

final class CartRuleAutoProcessor implements CartProcessorInterface
{
    public function __construct(private CartPriceRuleRepositoryInterface $cartPriceRuleRepository, private CartPriceRuleProcessorInterface $cartPriceRuleProcessor, private CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor)
    {
    }

    public function process(OrderInterface $cart): void
    {
        $eligibleRules = $this->cartPriceRuleRepository->findNonVoucherRules();

        foreach ($eligibleRules as $eligibleRule) {
            if (!$this->cartPriceRuleProcessor->process($cart, $eligibleRule)) {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $eligibleRule);
            }
        }
    }
}
