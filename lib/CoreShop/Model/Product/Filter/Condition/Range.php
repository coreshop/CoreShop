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
use Pimcore\Model\Object\QuantityValue\Unit;

/**
 * Class Range
 * @package CoreShop\Model\Product\Filter\Condition
 */
class Range extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'range';

    /**
     * @var mixed
     */
    public $preSelectMin;

    /**
     * @var mixed
     */
    public $preSelectMax;

    /**
     * @var float
     */
    public $stepCount;

    /**
     * @param mixed $preSelectMin
     */
    public function setPreSelectMin($preSelectMin)
    {
        $this->preSelectMin = $preSelectMin;
    }

    /**
     * @return mixed
     */
    public function getPreSelectMin()
    {
        return $this->preSelectMin;
    }

    /**
     * @param mixed $preSelectMax
     */
    public function setPreSelectMax($preSelectMax)
    {
        $this->preSelect = $preSelectMax;
    }

    /**
     * @return mixed
     */
    public function getPreSelectMax()
    {
        return $this->preSelectMax;
    }

    /**
     * @return float
     */
    public function getStepCount()
    {
        return $this->stepCount;
    }

    /**
     * @param float $stepCount
     */
    public function setStepCount($stepCount)
    {
        $this->stepCount = $stepCount;
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

        $product = [];
        foreach (['value'] as $value) {
            $product[$value] = array_product(array_column($rawValues, $value));
        }

        $minValue = count($rawValues) > 0 ? $rawValues[0]['value'] : 0;
        $maxValue = count($rawValues) > 0 ? $rawValues[count($rawValues)-1]['value'] : 0;
        $isFloat = count($rawValues) > 0 ? is_float($product['value']) : 0;

        return $this->getView()->partial($script, [
            'label' => $this->getLabel(),
            'minValue' => $minValue,
            'maxValue' => $maxValue,
            'isFloat' => $isFloat,
            'currentValueMin' => $currentFilter[$this->getField().'-min'] ? $currentFilter[$this->getField().'-min'] : $minValue,
            'currentValueMax' => $currentFilter[$this->getField().'-max'] ? $currentFilter[$this->getField().'-max'] : $maxValue,
            'values' => array_values($rawValues),
            'fieldname' => $this->getField(),
            'stepCount' => $this->getStepCount(),
            'quantityUnit' => Unit::getById($this->getQuantityUnit())
        ]);
    }

    /**
     * add Condition to Product list.
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
        if (array_key_exists($this->getField(), $params)) {
            $values = explode(",", $params[$this->getField()]);

            $params[$this->getField().'-min'] = $values[0];
            $params[$this->getField().'-max'] = $values[1];
        }

        $valueMin = $params[$this->getField().'-min'];
        $valueMax = $params[$this->getField().'-max'];

        if (empty($valueMax)) {
            $valueMax = $this->getPreSelect();
        }

        if ($valueMax === Filter\Service::EMPTY_STRING) {
            $valueMax = null;
        }

        if (empty($valueMin)) {
            $valueMin = $this->getPreSelectMin();
        }
        if ($valueMin === Filter\Service::EMPTY_STRING) {
            $valueMin = null;
        }

        $currentFilter[$this->getField().'-min'] = $valueMin;
        $currentFilter[$this->getField().'-max'] = $valueMax;

        if (!empty($valueMin) && !empty($valueMax)) {
            $fieldName = $this->getField();

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_' . $fieldName;
            }

            $list->addCondition(Condition::range($this->getField(), $valueMin, $valueMax), $fieldName);
        }

        return $currentFilter;
    }
}
