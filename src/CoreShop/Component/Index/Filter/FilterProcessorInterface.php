<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Filter;

use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface FilterProcessorInterface
{
    /**
     * @param FilterInterface  $filter
     * @param ListingInterface $list
     * @param ParameterBag     $parameterBag
     *
     * @return mixed
     */
    public function processConditions(FilterInterface $filter, ListingInterface $list, ParameterBag $parameterBag);

    /**
     * @param FilterInterface  $filter
     * @param ListingInterface $list
     * @param array            $currentFilter
     *
     * @return mixed
     */
    public function prepareConditionsForRendering(FilterInterface $filter, ListingInterface $list, $currentFilter);
}
