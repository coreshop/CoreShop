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

namespace CoreShop\Bundle\ResourceBundle\Validator\Constraints;

use CoreShop\Component\Resource\Exception\UnexpectedTypeException;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Webmozart\Assert\Assert;

final class UniqueEntityValidator extends ConstraintValidator
{
    /**
     * @param Concrete   $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        Assert::isInstanceOf($entity, Concrete::class);

        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
        }

        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        $fields = (array) $constraint->fields;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        if (null === $entity) {
            return;
        }

        $errorPath = $fields[0];
        $criteria = [];
        foreach ($fields as $fieldName) {
            $getter = 'get' . ucfirst($fieldName);
            if (!method_exists($entity, $getter)) {
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Concrete, so it cannot be validated for uniqueness.', $fieldName));
            }
            $criteria[$fieldName] = $entity->$getter();
        }

        $values = (array) $constraint->values;

        foreach ($values as $field => $value) {
            $criteria[$field] = $value;
        }

        $condition = [];
        $values = [];
        foreach ($criteria as $criteriaName => $criteriaValue) {
            if (is_array($criteriaValue)) {
                $subConditions = [];

                foreach ($criteriaValue as $criteriaSubValue) {
                    if (is_null($criteriaSubValue)) {
                        $subConditions[] = $criteriaName . ' IS NULL';
                    } else {
                        $subConditions[] = $criteriaName . ' = ?';
                        $values[] = $criteriaSubValue;
                    }
                }

                $condition[] = '(' . implode(' OR ', $subConditions) . ')';
            } else {
                $condition[] = $criteriaName . ' = ?';
                $values[] = $criteriaValue;
            }
        }

        $list = $entity::getList();
        $list->setCondition(implode(' AND ', $condition), $values);
        $elements = $list->load();

        if (count($elements) > 0) {
            if ($constraint->allowSameEntity && count($elements) === 1 && $entity->getId() === $elements[0]->getId()) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->atPath($errorPath)
                ->setParameter('{{ value }}', $criteria[$fields[0]])
                ->setInvalidValue($criteria[$fields[0]])
                ->setCause($entity)
                ->addViolation();
        }
    }
}
