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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;

final class CartRuleAutoProcessor implements CartProcessorInterface
{
    /**
     * @var CartPriceRuleRepositoryInterface
     */
    private $cartPriceRuleRepository;

    /**
     * @var CartPriceRuleProcessorInterface
     */
    private $cartPriceRuleProcessor;

    /**
     * @param CartPriceRuleRepositoryInterface $cartPriceRuleRepository
     * @param CartPriceRuleProcessorInterface  $cartPriceRuleProcessor
     */
    public function __construct(
        CartPriceRuleRepositoryInterface $cartPriceRuleRepository,
        CartPriceRuleProcessorInterface $cartPriceRuleProcessor
    ) {
        $this->cartPriceRuleRepository = $cartPriceRuleRepository;
        $this->cartPriceRuleProcessor = $cartPriceRuleProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $eligibleRules = $this->cartPriceRuleRepository->findNonVoucherRules();

        foreach ($eligibleRules as $eligibleRule) {
            $this->cartPriceRuleProcessor->process($cart, $eligibleRule);
        }
    }
}
