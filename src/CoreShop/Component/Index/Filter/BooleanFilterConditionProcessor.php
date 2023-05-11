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

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\MatchCondition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class BooleanFilterConditionProcessor implements FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(
        FilterConditionInterface $condition,
        FilterInterface $filter,
        ListingInterface $list,
        array $currentFilter,
    ): array {
        $field = $condition->getConfiguration()['field'];

        $rawValues = $list->getGroupByValues($field, true);

        return [
            'type' => 'boolean',
            'label' => $condition->getLabel(),
            'currentValue' => $currentFilter[$field] ?? null,
            'values' => array_values($rawValues),
            'fieldName' => $field,
            'quantityUnit' => $condition->getQuantityUnit() ? Unit::getById($condition->getQuantityUnit()) : null,
        ];
    }

    public function addCondition(
        FilterConditionInterface $condition,
        FilterInterface $filter,
        ListingInterface $list,
        array $currentFilter,
        ParameterBag $parameterBag,
        bool $isPrecondition = false,
    ): array {
        $field = $condition->getConfiguration()['field'];
        $value = $parameterBag->get($field);

        if (!$parameterBag->has($field) && isset($condition->getConfiguration()['preSelect'])) {
            $value = $condition->getConfiguration()['preSelect'];
        }

        if (null !== $value) {
            $value = filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);

            $currentFilter[$field] = $value;
            $fieldName = $field;

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_' . $fieldName;
            }

            $list->addCondition(new MatchCondition($field, (string) $value), $fieldName);
        }

        return $currentFilter;
    }
}
