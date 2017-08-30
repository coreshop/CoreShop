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

class SelectFilterConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter)
    {
        $rawValues = $list->getGroupByValues($condition->getField(), true);

        return [
            'type' => 'select',
            'label' => $condition->getLabel(),
            'currentValue' => $currentFilter[$condition->getField()],
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
        $value = $parameterBag->get($condition->getField());

        if (empty($value)) {
            $value = $condition->getConfiguration()['preSelect'];
        }

        $value = trim($value);

        $currentFilter[$condition->getField()] = $value;

        if (!empty($value)) {
            $fieldName = $condition->getField();

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_'.$fieldName;
            }

            $list->addCondition(Condition::match($condition->getField(), $value), $fieldName);
        }

        return $currentFilter;
    }
}
