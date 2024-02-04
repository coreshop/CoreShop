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

namespace CoreShop\Bundle\ResourceBundle\Validator\Constraints;

use CoreShop\Component\Resource\Exception\UnexpectedTypeException;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Webmozart\Assert\Assert;

final class UniqueEntityValidator extends ConstraintValidator
{
    public function __construct(
        protected ExpressionLanguage $expressionLanguage,
        protected ContainerInterface $container,
    ) {
    }

    protected function evaluateExpression(array $value): array
    {
        foreach ($value as $i => $item) {
            $value[$i] = $this->expressionLanguage->evaluate($item, ['container' => $this->container]);
        }

        return $value;
    }

    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var Concrete $value
         */
        Assert::isInstanceOf($value, Concrete::class);

        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
        }

        $fields = $this->evaluateExpression($constraint->fields);

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        $errorPath = $fields[0];
        $criteria = [];
        foreach ($fields as $fieldName) {
            $getter = 'get' . ucfirst($fieldName);
            if (!method_exists($value, $getter)) {
                throw new ConstraintDefinitionException(
                    sprintf(
                        'The field "%s" is not mapped by Concrete, so it cannot be validated for uniqueness.',
                        $fieldName,
                    ),
                );
            }
            $criteria[$fieldName] = $value->$getter();
        }

        $values = $constraint->values;

        foreach ($values as $field => $fieldValue) {
            $criteria[$field] = $fieldValue;
        }

        $condition = [];
        $values = [];
        foreach ($criteria as $criteriaName => $criteriaValue) {
            if (is_array($criteriaValue)) {
                $subConditions = [];

                foreach ($criteriaValue as $criteriaSubValue) {
                    if (null === $criteriaSubValue) {
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

        /**
         * @var DataObject\Listing\Concrete $list
         */
        $list = $value::getList();
        $list->setCondition(implode(' AND ', $condition), $values);
        $list->setUnpublished(true);
        $elements = $list->load();

        if (count($elements) > 0) {
            $foundElement = $elements[0];

            if ($constraint->allowSameEntity && count($elements) === 1 && $value->getId() === $foundElement->getId()) {
                return;
            }

            $this->context->buildViolation($this->evaluateExpression([$constraint->message])[0])
                ->atPath($errorPath)
                ->setParameter('{{ value }}', $criteria[$fields[0]])
                ->setInvalidValue($criteria[$fields[0]])
                ->setCause($value)
                ->addViolation()
            ;
        }
    }
}
