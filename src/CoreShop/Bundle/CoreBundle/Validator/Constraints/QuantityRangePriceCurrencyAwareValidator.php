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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class QuantityRangePriceCurrencyAwareValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof QuantityRangePriceCurrencyAware) {
            throw new UnexpectedTypeException($constraint, QuantityRangePriceCurrencyAware::class);
        }

        if (!$value instanceof QuantityRangeInterface) {
            throw new UnexpectedTypeException($value, QuantityRangeInterface::class);
        }

        if (!$value->getCurrency() instanceof CurrencyInterface) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ rangeStartingFrom }}', sprintf('Range starting from %d', $value->getRangeStartingFrom()))
                ->addViolation()
            ;
        }
    }
}
