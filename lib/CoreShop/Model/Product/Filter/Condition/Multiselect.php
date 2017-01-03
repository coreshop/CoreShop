<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Filter\Condition;

use CoreShop\IndexService\Condition;
use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

/**
 * Class Multiselect
 * @package CoreShop\Model\Product\Filter\Condition
 */
class Multiselect extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'multiselect';

    /**
     * @var mixed
     */
    public $preSelects;

    /**
     * @return mixed
     */
    public function getPreSelects()
    {
        return $this->preSelects;
    }

    /**
     * @param mixed $preSelects
     */
    public function setPreSelects($preSelects)
    {
        $this->preSelects = $preSelects;
    }

    /**
     * render HTML for filter.
     *
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     *
     * @return mixed
     */
    public function render(Filter $filter, Listing $list, $currentFilter)
    {
        $rawValues = $list->getGroupByValues($this->getField(), true);
        $script = $this->getViewScript($filter, $list, $currentFilter);

        return $this->getView()->partial($script, [
            'label' => $this->getLabel(),
            'currentValues' => $currentFilter[$this->getField()],
            'values' => array_values($rawValues),
            'fieldname' => $this->getField(),
            'quantityUnit' => $this->getQuantityUnit()
        ]);
    }

    /**
     * add Condition to Product List.
     *
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     * @param $params
     * @param bool $isPrecondition
     *
     * @return array $currentFilter
     */
    public function addCondition(Filter $filter, Listing $list, $currentFilter, $params, $isPrecondition = false)
    {
        $values = $params[$this->getField()];

        if (empty($values)) {
            $values = $this->getPreSelects();
        }

        $currentFilter[$this->getField()] = $values;

        if ($values === Filter\Service::EMPTY_STRING) {
            $values = null;
        }

        if (!empty($values)) {
            $fieldName = $isPrecondition ? 'PRECONDITION_'.$this->getField() : $this->getField();

            if (!empty($values)) {
                $list->addCondition(Condition::in($fieldName, $values), $fieldName);
            }
        }

        return $currentFilter;
    }
}
