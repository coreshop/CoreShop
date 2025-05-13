<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Validator\Constraints;

use CoreShop\Component\Order\Generator\CodeGeneratorCheckerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class VoucherAmountValidator extends ConstraintValidator
{
    public function __construct(
        private CodeGeneratorCheckerInterface $checker,
    ) {
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
                ],
            );
        }
    }
}
