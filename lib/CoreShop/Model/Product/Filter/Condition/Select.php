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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Product\Filter\Condition;

use CoreShop\IndexService\Condition;
use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

/**
 * Class Select
 * @package CoreShop\Model\Product\Filter\Condition
 */
class Select extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'select';

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
        $value = $params[$this->getField()];

        if (empty($value)) {
            $value = $this->getPreSelect();
        }

        if ($value === Filter\Service::EMPTY_STRING) {
            $value = null;
        }

        $value = trim($value);

        $currentFilter[$this->getField()] = $value;

        if (!empty($value)) {
            $fieldName = $this->getField();

            if ($isPrecondition) {
                $fieldName = 'PRECONDITION_' . $fieldName;
            }

            $list->addCondition(Condition::match($this->getField(), $value), $fieldName);
        }

        return $currentFilter;
    }
}
