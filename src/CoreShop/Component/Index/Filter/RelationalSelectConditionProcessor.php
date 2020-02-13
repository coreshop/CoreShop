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

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Condition\MatchCondition;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\HttpFoundation\ParameterBag;

class RelationalSelectConditionProcessor implements FilterConditionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter): array
    {
        $field = $condition->getConfiguration()['field'];

        $rawValues = $list->getGroupByRelationValues($field, true);
        $objects = [];

        foreach ($rawValues as $value) {
            $object = Concrete::getById($value['value']);
            if ($object instanceof Concrete) {
                $objects[] = $object;
            }
        }

        return [
            'type' => 'relational_select',
            'label' => $condition->getLabel(),
            'currentValues' => $currentFilter[$field],
            'values' => $rawValues,
            'objects' => $objects,
            'fieldName' => $field,
            'quantityUnit' => Unit::getById($condition->getQuantityUnit()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, array $currentFilter, ParameterBag $parameterBag, bool $isPrecondition = false): array
    {
        $field = $condition->getConfiguration()['field'];
        $value = $parameterBag->get($field);

        if (empty($value)) {
            $value = $condition->getConfiguration()['preSelect'];
        }

        $value = trim($value);

        if (!empty($value)) {
            $currentFilter[$field] = $value;

            $fieldName = $field;

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_' . $fieldName;
            }

            $list->addRelationCondition(new MatchCondition('dest', $value), $fieldName);
        }

        return $currentFilter;
    }
}
