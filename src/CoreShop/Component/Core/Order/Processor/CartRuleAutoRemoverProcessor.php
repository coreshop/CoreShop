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

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartRuleAutoRemoverProcessor implements CartProcessorInterface
{
    /**
     * @var CartPriceRuleValidationProcessorInterface
     */
    private $cartPriceRuleValidator;

    /**
     * @var CartPriceRuleUnProcessorInterface
     */
    private $cartPriceRuleUnProcessor;

    /**
     * @param CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator
     * @param CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor
     */
    public function __construct(
        CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator,
        CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor
    )
    {
        $this->cartPriceRuleValidator = $cartPriceRuleValidator;
        $this->cartPriceRuleUnProcessor = $cartPriceRuleUnProcessor;
    }


    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $priceRuleItems = $cart->getPriceRuleItems();

        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $item) {
            if (!$item instanceof ProposalCartPriceRuleItemInterface) {
                continue;
            }

            if ($item->getCartPriceRule()->getIsVoucherRule()) {
                continue;
            }

            if (!$this->cartPriceRuleValidator->isValidCartRule($cart, $item->getCartPriceRule())) {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule());
            }
        }
    }
}