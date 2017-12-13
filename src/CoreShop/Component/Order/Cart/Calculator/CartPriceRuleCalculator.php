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

namespace CoreShop\Component\Order\Cart\Calculator;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Webmozart\Assert\Assert;

class CartPriceRuleCalculator implements CartDiscountCalculatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $actionServiceRegistry;

    /**
     * @var RuleValidationProcessorInterface
     */
    protected $ruleValidationProcessor;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    protected $voucherCodeRepository;

    /**
     * @param ServiceRegistryInterface $actionServiceRegistry
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     */
    public function __construct(
        ServiceRegistryInterface $actionServiceRegistry,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
    )
    {
        $this->actionServiceRegistry = $actionServiceRegistry;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $withTax = true)
    {
        Assert::isInstanceOf($subject, CartInterface::class);

        $discount = 0;

        /**
         * @var $subject CartInterface
         * @var $rules RuleInterface[]
         */
        $rules = $subject->getPriceRules();

        $ruleItems = $subject->getPriceRuleItems();

        if (!$ruleItems instanceof Fieldcollection) {
            return 0;
        }

        foreach ($ruleItems->getItems() as $ruleItem) {
            if (!$ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($ruleItem->getVoucherCode());
            $rule = $ruleItem->getCartPriceRule();

            if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                continue;
            }

            if ($this->ruleValidationProcessor->isValid(['cart' => $subject, 'voucher' => $voucherCode], $rule)) {
                foreach ($rule->getActions() as $action) {
                    $processor = $this->actionServiceRegistry->get($action->getType());

                    if ($processor instanceof CartPriceRuleActionProcessorInterface) {
                        $discount += $processor->getDiscount($subject, $withTax, $action->getConfiguration());
                    }
                }
            }
        }

        return $discount;
    }
}
