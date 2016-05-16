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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\Product\Filter\Condition;

use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

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
            if ($isPrecondition) {
                $list->addCondition('TRIM(`'.$this->getField().'`) = '.$list->quote($value), 'PRECONDITION_'.$this->getField());
            } else {
                $list->addCondition('TRIM(`'.$this->getField().'`) = '.$list->quote($value), $this->getField());
            }
        }

        return $currentFilter;
    }
}
