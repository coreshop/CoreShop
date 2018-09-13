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

namespace CoreShop\Bundle\OrderBundle\Validator\Constraints;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartPriceRuleValidator extends ConstraintValidator
{
    /**
     * @var CartPriceRuleValidationProcessorInterface
     */
    private $ruleValidationProcessor;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $voucherCodeRepository;

    /**
     * @param CartPriceRuleValidationProcessorInterface $ruleValidationProcessor
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     */
    public function __construct(CartPriceRuleValidationProcessorInterface $ruleValidationProcessor, CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository)
    {
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->voucherCodeRepository = $voucherCodeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var CartPriceRule $constraint */
        Assert::isInstanceOf($constraint, CartPriceRule::class);

        if (!$value instanceof CartInterface) {
            return;
        }

        $ruleItems = $value->getPriceRuleItems();

        if (!$ruleItems instanceof Fieldcollection) {
            return;
        }

        foreach ($ruleItems as $ruleItem) {
            if (!$ruleItem instanceof ProposalCartPriceRuleItemInterface) {
                return;
            }

            $cartRule = $ruleItem->getCartPriceRule();

            if (!$cartRule instanceof CartPriceRuleInterface) {
                //Add violation?
                return;
            }

            if (!$cartRule->getIsVoucherRule()) {
                continue;
            }

            $voucherCode = $this->voucherCodeRepository->findByCode($ruleItem->getVoucherCode());

            if ($voucherCode && !$this->ruleValidationProcessor->isValidCartRule($value, $cartRule, $voucherCode)) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%rule%' => $cartRule->getName()]
                );
            }
        }
    }
}
