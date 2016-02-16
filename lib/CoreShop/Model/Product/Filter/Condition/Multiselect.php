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

class Multiselect extends AbstractCondition
{
    /**
     * @var string
     */
    public $type = "multiselect";

    /**
     * add Condition to Productlist
     *
     * @param Filter $filter
     * @param Listing $list
     * @param $currentFilter
     * @param $params
     * @param bool $isPrecondition
     * @return array $currentFilter
     */
    public function addCondition(Filter $filter, Listing $list, $currentFilter, $params, $isPrecondition = false) {

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

    }
}
