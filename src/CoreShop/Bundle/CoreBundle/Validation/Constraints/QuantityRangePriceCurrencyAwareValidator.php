<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Validation\Constraints;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class QuantityRangePriceCurrencyAwareValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof QuantityRangePriceCurrencyAware) {
            throw new UnexpectedTypeException($constraint, QuantityRangePriceCurrencyAware::class);
        }

        if (!$value instanceof QuantityRangeInterface) {
            throw new UnexpectedTypeException($value, QuantityRangeInterface::class);
        }

        if (!$value->getCurrency() instanceof CurrencyInterface) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ range }}', sprintf('Range from %d to %d', $value->getRangeFrom(), $value->getRangeTo()))
                ->addViolation();
        }
    }
}
