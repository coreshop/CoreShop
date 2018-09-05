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
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Cart\Rule\ProposalCartPriceRuleCalculatorInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartPriceRuleProcessor implements CartProcessorInterface
{
    /**
     * @var ProposalCartPriceRuleCalculatorInterface
     */
    private $proposalCartPriceRuleCalculator;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $voucherCodeRepository;

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var CartPriceRuleValidationProcessorInterface
     */
    private $cartPriceRuleValidator;

    /**
     * @var CartPriceRuleUnProcessorInterface
     */
    private $cartPriceRuleUnProcessor;

    /**
     * @param ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     * @param CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator
     * @param CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(
        ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator,
        CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor,
        AdjustmentFactoryInterface $adjustmentFactory
    )
    {
        $this->proposalCartPriceRuleCalculator = $proposalCartPriceRuleCalculator;
        $this->voucherCodeRepository = $voucherCodeRepository;
        $this->cartPriceRuleValidator = $cartPriceRuleValidator;
        $this->cartPriceRuleUnProcessor = $cartPriceRuleUnProcessor;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $cart->removeAdjustments(AdjustmentInterface::CART_PRICE_RULE);

        $priceRuleItems = $cart->getPriceRuleItems();

        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $index => $item) {
            if (!$item instanceof ProposalCartPriceRuleItemInterface) {
                continue;
            }

            if (!$item->getCartPriceRule() instanceof CartPriceRuleInterface) {
                $priceRuleItems->remove($index);
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($item->getVoucherCode());

            if (!$item->getCartPriceRule()->getIsVoucherRule() && null !== $item->getVoucherCode()) {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule(), $voucherCode);
            }

            if ($this->cartPriceRuleValidator->isValidCartRule($cart, $item->getCartPriceRule(), $voucherCode)) {
                $rule = $this->proposalCartPriceRuleCalculator->calculatePriceRule(
                    $cart,
                    $item->getCartPriceRule(),
                    $voucherCode
                );

                if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                    $cart->addAdjustment(
                        $this->adjustmentFactory->createWithData(
                            AdjustmentInterface::CART_PRICE_RULE,
                            $item->getCartPriceRule()->getName(),
                            -1 * $rule->getDiscount(true),
                            -1 * $rule->getDiscount(false)
                        )
                    );
                }
            }
            else {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule(), $voucherCode);
            }
        }
    }
}