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
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
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
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleVoucherCodeInterface $voucher, array $configuration)
    {
        Assert::isInstanceOf($cart, CartInterface::class);

        $maxUsagePerCode = $configuration['maxUsagePerCode'];
        $onlyOncePerCart = $configuration['onlyOncePerCart'];

        $storedCode = $this->voucherCodeRepository->findByCode($voucher->getCode());

        if (!$storedCode instanceof CartPriceRuleVoucherCodeInterface) {
            return false;
        }

        if (is_numeric($maxUsagePerCode) && $storedCode->getUses() >= $maxUsagePerCode) {
            return false;
        }

        if ($onlyOncePerCart === true) {

            /**
             * @var $subject CartInterface
             * @var $rules RuleInterface[]
             */
            $rules = $subject->getPriceRules();

            $valid = true;
            foreach ($rules as $rule) {
                if ($rule instanceof CartPriceRuleInterface) {
                    if($rule->hasVoucherCode($storedCode)) {
                       $valid = false;
                       break;
                    }
                }
            }

            return $valid;
        }
        return true;
    }
}
