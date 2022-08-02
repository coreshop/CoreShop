<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Validator\Constraints;

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleValidationProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartPriceRuleValidator extends ConstraintValidator
{
    public function __construct(private CartPriceRuleValidationProcessorInterface $ruleValidationProcessor, private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CartPriceRule $constraint */
        Assert::isInstanceOf($constraint, CartPriceRule::class);

        if (!$value instanceof OrderInterface) {
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
