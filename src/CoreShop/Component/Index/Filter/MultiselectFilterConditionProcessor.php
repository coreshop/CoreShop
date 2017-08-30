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

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\Condition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class MultiselectFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $rawValues = $list->getGroupByValues($condition->getField(), true);

        return [
            'type' => 'multiselect',
            'label' => $condition->getLabel(),
            'currentValues' => $currentFilter[$condition->getField()],
            'values' => array_values($rawValues),
            'fieldName' => $condition->getField(),
            'quantityUnit' => Unit::getById($condition->getQuantityUnit()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false)
    {
        $values = $parameterBag->get($condition->getField());

        if (empty($values)) {
            $values = $condition->getConfiguration()['preSelects'];
        }

        $currentFilter[$condition->getField()] = $values;

        if ($values === static::EMPTY_STRING) {
            $values = null;
        }

        if (!empty($values)) {
            $fieldName = $isPrecondition ? 'PRECONDITION_'.$condition->getField() : $condition->getField();

            if (!empty($values)) {
                $list->addCondition(Condition::in($fieldName, $values), $fieldName);
            }
        }

        return $currentFilter;
    }
}
