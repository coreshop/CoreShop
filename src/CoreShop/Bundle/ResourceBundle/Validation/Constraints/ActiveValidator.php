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

namespace CoreShop\Bundle\ResourceBundle\Validation\Constraints;

use CoreShop\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ActiveValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Active) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Active');
        }

        if ($value instanceof ToggleableInterface) {
            if (!$value->getActive()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(Active::IS_NOT_ENABLED_ERROR)
                    ->addViolation();
            }
        } else {
            throw new UnexpectedTypeException($value, ToggleableInterface::class);
        }
    }
}
