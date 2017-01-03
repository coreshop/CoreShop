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
 * Class Combined
 * @package CoreShop\Model\Product\Filter\Condition
 */
class Combined extends AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'combined';

    /**
     * @var mixed
     */
    public $conditions;

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param mixed $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
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
        $script = $this->getViewScript($filter, $list, $currentFilter);

        return $this->getView()->partial($script, [
            'label' => !$this->getLabel() ? " " : $this->getLabel(),
            'conditions' => is_array($this->getConditions()) ? $this->getConditions() : [],
            'filter' => $filter,
            'list' => $list,
            'currentFilter' => $currentFilter
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
        $filters = [];

        if ($this->getConditions() && is_array($this->getConditions())) {
            foreach ($this->getConditions() as $element) {
                if ($element instanceof AbstractCondition) {
                    $filters = array_merge($filters, $element->addCondition($filter, $list, $currentFilter, $params, $isPrecondition));
                }
            }
        }

        return $filters;
    }
}
