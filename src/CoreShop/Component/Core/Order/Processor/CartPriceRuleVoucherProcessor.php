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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\ProposalCartPriceRuleCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\Fieldcollection;

final class CartPriceRuleVoucherProcessor implements CartProcessorInterface
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
     * @var CartPriceRuleValidationProcessorInterface
     */
    private $cartPriceRuleValidator;

    /**
     * @var CartPriceRuleUnProcessorInterface
     */
    private $cartPriceRuleUnProcessor;

    /**
     * @param ProposalCartPriceRuleCalculatorInterface  $proposalCartPriceRuleCalculator
     * @param CartPriceRuleVoucherRepositoryInterface   $voucherCodeRepository
     * @param CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator
     * @param CartPriceRuleUnProcessorInterface         $cartPriceRuleUnProcessor
     */
    public function __construct(
        ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator,
        CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor
    ) {
        $this->proposalCartPriceRuleCalculator = $proposalCartPriceRuleCalculator;
        $this->voucherCodeRepository = $voucherCodeRepository;
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

        foreach ($priceRuleItems->getItems() as $index => $item) {
            if (!$item instanceof ProposalCartPriceRuleItemInterface) {
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
                    $voucherCode
                );
            } else {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $item->getCartPriceRule(), $voucherCode);
            }
        }
    }
}
