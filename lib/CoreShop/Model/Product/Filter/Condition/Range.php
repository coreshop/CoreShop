<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Filter\Condition;

use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

class Range extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = "range";


    /**
     * @var mixed
     */
    public $preSelectMin;

    /**
     * @var mixed
     */
    public $preSelectMax;

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
     * render HTML for filter
     *
     * @param Filter $filter
     * @param Listing $list
     * @param $currentFilter
     * @return mixed
     */
    public function render(Filter $filter, Listing $list, $currentFilter) {

        $rawValues = $list->getGroupByValues($this->getField(), true);
        $script = $this->getType() . ".php";

        if($this->getView()->getScriptPath($this->getField() . ".php")) {
            $script = $this->getField() . ".php";
        }

        return $this->getView()->partial($script, array(
            "label" => $this->getLabel(),
            "currentValueMin" => $currentFilter[$this->getField() . '-min'],
            "currentValueMax" => $currentFilter[$this->getField() . '-max'],
            "values" => array_values($rawValues),
            "fieldname" => $this->getField()
        ));
    }

    /**
     * add Condition to Product list
     *
     * @param Filter $filter
     * @param Listing $list
     * @param $params
     * @param bool $isPrecondition
     * @return mixed
     */
    public function addCondition(Filter $filter, Listing $list, $params, $isPrecondition = false) {

        $valueMin = $params[$this->getField() . '-min'];
        $valueMax = $params[$this->getField() . '-max'];

        if($isPrecondition && empty($valueMin)) {
            $valueMin = (int) $this->getPreSelectMin();
        }

        if($isPrecondition && empty($valueMax)) {
            $valueMax = (int) $this->getPreSelectMax();
        }

        if($valueMin == Filter\Service::EMPTY_STRING) {
            $valueMin = null;
        }

        if($valueMax == Filter\Service::EMPTY_STRING) {
            $valueMax = null;
        }

        if(!empty($valueMin) && !empty($valueMax)) {
            if($isPrecondition) {
                $list->addCondition("TRIM(`" . $this->getField() . "`) >= " . $valueMin . " AND TRIM(`" . $this->getField() . "`) <= " . $valueMax, "PRECONDITION_" . $this->getField());
            } else {
                $list->addCondition("TRIM(`" . $this->getField() . "`) >= " . $valueMin . " AND TRIM(`" . $this->getField() . "`) <= " . $valueMax, $this->getField());
            }
        }
    }
}
