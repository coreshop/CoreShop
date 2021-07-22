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

namespace CoreShop\Bundle\OrderBundle\Validator\Constraints;

use CoreShop\Component\Order\Generator\CodeGeneratorCheckerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class VoucherAmountValidator extends ConstraintValidator
{
    /** @var CodeGeneratorCheckerInterface */
    private $checker;

    public function __construct(CodeGeneratorCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($generator, Constraint $constraint): void
    {
        /** @var CartPriceRuleVoucherGeneratorInterface $generator */
        Assert::isInstanceOf($generator, CartPriceRuleVoucherGeneratorInterface::class);

        /** @var VoucherAmount $constraint */
        Assert::isInstanceOf($constraint, VoucherAmount::class);

        if (null === $generator->getLength() || null === $generator->getAmount()) {
            return;
        }

        if (!$this->checker->isGenerationPossible($generator)) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '%expectedAmount%' => $generator->getAmount(),
                    '%codeLength%' => $generator->getLength(),
                    '%possibleAmount%' => $this->checker->getPossibleGenerationAmount($generator),
                ]
            );
        }
    }
}
