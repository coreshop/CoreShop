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

use CoreShop\Component\Order\Cart\Rule\ProposalCartPriceRuleCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
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
     * @param ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator
     * @param CartPriceRuleVoucherRepositoryInterface  $voucherCodeRepository
     */
    public function __construct(
        ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
    ) {
        $this->proposalCartPriceRuleCalculator = $proposalCartPriceRuleCalculator;
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        $cart->setDiscount(0, false);
        $cart->setDiscount(0, true);

        $priceRuleItems = $cart->getPriceRuleItems();

        if (!$priceRuleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($priceRuleItems->getItems() as $item) {
            if (!$item instanceof ProposalCartPriceRuleItemInterface) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($item->getVoucherCode());

            $rule = $this->proposalCartPriceRuleCalculator->calculatePriceRule($cart, $item->getCartPriceRule(), $voucherCode);

            if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                $cart->setDiscount($cart->getDiscount(false) + $rule->getDiscount(false), false);
                $cart->setDiscount($cart->getDiscount(true) + $rule->getDiscount(true), true);
            }
        }
    }
}
