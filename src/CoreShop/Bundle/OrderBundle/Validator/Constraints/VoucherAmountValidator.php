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

namespace CoreShop\Bundle\OrderBundle\Validator\Constraints;

use CoreShop\Component\Order\Generator\CodeGeneratorCheckerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class VoucherAmountValidator extends ConstraintValidator
{
    private CodeGeneratorCheckerInterface $checker;

    public function __construct(CodeGeneratorCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CartPriceRuleVoucherGeneratorInterface $value */
        Assert::isInstanceOf($value, CartPriceRuleVoucherGeneratorInterface::class);

        /** @var VoucherAmount $constraint */
        Assert::isInstanceOf($constraint, VoucherAmount::class);

        if (null === $value->getLength() || null === $value->getAmount()) {
            return;
        }

        if (!$this->checker->isGenerationPossible($value)) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '%expectedAmount%' => $value->getAmount(),
                    '%codeLength%' => $value->getLength(),
                    '%possibleAmount%' => $this->checker->getPossibleGenerationAmount($value),
                ]
            );
        }
    }
}
