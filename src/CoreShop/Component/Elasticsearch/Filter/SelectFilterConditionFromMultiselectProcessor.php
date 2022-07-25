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

use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Filter\FilterConditionProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class SelectFilterConditionFromMultiselectProcessor implements FilterConditionProcessorInterface
{
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $field = $condition->getConfiguration()['field'];

        $rawValues = $list->getGroupByValues($field, true);
        $values = [];

        foreach ($rawValues as $v) {
            $explode = explode(',', $v['value']);

            foreach ($explode as $e) {
                if (empty($e)) {
                    continue;
                }

                if (array_key_exists($e, $values)) {
                    $values[$e]['count'] += $v['count'];

                    continue;
                }

                $values[$e] = ['value' => $e, 'count' => $v['count']];
            }
        }

        return [
            'type' => 'select',
            'label' => $condition->getLabel(),
            'currentValue' => isset($currentFilter[$field]) ? trim($currentFilter[$field], ',') : null,
            'values' => array_values($values),
            'fieldName' => $field,
            'quantityUnit' => $condition->getQuantityUnit() ? Unit::getById($condition->getQuantityUnit()) : null,
        ];
    }

    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter, ParameterBag $parameterBag, bool $isPrecondition = false): array
    {
        $field = $condition->getConfiguration()['field'];
        $value = $parameterBag->get($field);

        if (empty($value)) {
            $value = $condition->getConfiguration()['preSelect'];
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        if (!empty($value)) {
            $value = ',' . $value . ',';

            $currentFilter[$field] = $value;

            $fieldName = $field;

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_' . $fieldName;
            }

            $list->addCondition(new LikeCondition($field, 'both', $value), $fieldName);
        }

        return $currentFilter;
    }
}
