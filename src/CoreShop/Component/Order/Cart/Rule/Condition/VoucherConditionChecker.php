<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    private $voucherCodeRepository;

    public function __construct(CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository)
    {
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, ?CartPriceRuleVoucherCodeInterface $voucher, array $configuration): bool
    {
        if (null === $voucher) {
            return false;
        }

        Assert::isInstanceOf($cart, CartInterface::class);

        $maxUsagePerCode = $configuration['maxUsagePerCode'];
        $onlyOnePerCart = $configuration['onlyOnePerCart'];

        $storedCode = $this->voucherCodeRepository->findByCode($voucher->getCode());

        if (!$storedCode instanceof CartPriceRuleVoucherCodeInterface) {
            return false;
        }

        // max usage per code condition
        if (is_numeric($maxUsagePerCode)) {
            if ($maxUsagePerCode != 0 && $storedCode->getUses() >= $maxUsagePerCode) {
                return false;
            }
        }

        // only once per cart condition
        if ($onlyOnePerCart === true) {
            $valid = true;
            if ($cart->hasPriceRules()) {
                foreach ($cart->getPriceRuleItems() as $rule) {
                    if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                        if ($rule->getCartPriceRule()->getIsVoucherRule() && $rule->getVoucherCode() !== $storedCode->getCode()) {
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
