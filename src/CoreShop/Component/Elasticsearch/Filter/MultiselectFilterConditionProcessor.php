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

declare(strict_types=1);

namespace CoreShop\Component\Elasticsearch\Filter;

use CoreShop\Component\Index\Condition\InCondition;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultiselectFilterConditionProcessor implements FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $field = $condition->getConfiguration()['field'];
        $rawValues = $list->getGroupByValues($field, true);

        return [
            'type' => 'multiselect',
            'label' => $condition->getLabel(),
            'currentValues' => $currentFilter[$field],
            'values' => array_values($rawValues),
            'fieldName' => $field,
            'quantityUnit' => $condition->getQuantityUnit() ? Unit::getById($condition->getQuantityUnit()) : null,
        ];
    }

    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter, ParameterBag $parameterBag, bool $isPrecondition = false): array
    {
        $field = $condition->getConfiguration()['field'];
        $values = $parameterBag->get($field);

        if (empty($values) && isset($condition->getConfiguration()['preSelects'])) {
            $values = $condition->getConfiguration()['preSelects'];
        }

        $currentFilter[$field] = $values;

        if ($values === static::EMPTY_STRING) {
            $values = null;
        }

        if (is_array($values) && !empty($values)) {
            $fieldName = $isPrecondition ? 'PRECONDITION_' . $field : $field;
            $list->addCondition(new InCondition($field, $values), $fieldName);
        }

        return $currentFilter;
    }
}
