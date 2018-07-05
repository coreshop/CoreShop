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

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface FilterConditionProcessorInterface
{
    /**
     * Const for Empty Value.
     */
    const EMPTY_STRING = '##EMPTY##';

    /**
     * @param FilterConditionInterface $condition
     * @param FilterInterface          $filter
     * @param ListingInterface         $list
     * @param $currentFilter
     * @param ParameterBag $parameterBag
     * @param bool         $isPrecondition
     *
     * @return mixed
     */
    public function addCondition(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter, ParameterBag $parameterBag, $isPrecondition = false);

    /**
     * @param FilterConditionInterface $condition
     * @param FilterInterface          $filter
     * @param ListingInterface         $list
     * @param $currentFilter
     *
     * @return array
     */
    public function prepareValuesForRendering(FilterConditionInterface $condition, FilterInterface $filter, ListingInterface $list, $currentFilter);
}
