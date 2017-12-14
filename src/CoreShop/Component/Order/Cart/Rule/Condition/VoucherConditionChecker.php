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
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Webmozart\Assert\Assert;

class VoucherConditionChecker extends AbstractConditionChecker
{
    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $voucherCodeRepository;

    /**
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     */
    public function __construct(CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository)
    {
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucher = null, array $configuration)
    {
        Assert::isInstanceOf($cart, CartInterface::class);

        $maxUsagePerCode = $configuration['maxUsagePerCode'];
        $onlyOncePerCart = $configuration['onlyOncePerCart'];

        $storedCode = $this->voucherCodeRepository->findByCode($voucher->getCode());

        if (!$storedCode instanceof CartPriceRuleVoucherCodeInterface) {
            return false;
        }

        // max usage per code condition
        if (is_numeric($maxUsagePerCode)) {
            $cartVoucherCounter = 0;
            if ($cart->hasPriceRules()) {
                foreach ($cart->getPriceRuleItems() as $rule) {
                    if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                        if (!empty($rule->getVoucherCode())) {
                            $cartVoucherCounter++;
                        }
                    }
                }
            }

            $fullCounter = $storedCode->getUses() + $cartVoucherCounter;
            if (is_numeric($maxUsagePerCode) && $fullCounter >= $maxUsagePerCode) {
                return false;
            }
        }

        // only once per cart condition
        if ($onlyOncePerCart === true) {
            $valid = true;
            if ($cart->hasPriceRules()) {
               foreach ($cart->getPriceRuleItems() as $rule) {
                   if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                       if ($rule->getVoucherCode() == $storedCode->getCode()) {
                           $valid = false;
                           break;
                       }
                    }
                }
            }

            return $valid;
        }
        return true;
    }
}
