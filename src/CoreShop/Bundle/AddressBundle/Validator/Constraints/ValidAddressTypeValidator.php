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

namespace CoreShop\Bundle\AddressBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidAddressTypeValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    protected $validAddressTypes;

    /**
     * @param array $validAddressTypes
     */
    public function __construct(array $validAddressTypes)
    {
        $this->validAddressTypes = $validAddressTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidAddressType) {
            throw new UnexpectedTypeException($constraint, ValidAddressType::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!in_array($value, $this->validAddressTypes, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%address_type%', $value)
                ->addViolation();
        }
    }
}
