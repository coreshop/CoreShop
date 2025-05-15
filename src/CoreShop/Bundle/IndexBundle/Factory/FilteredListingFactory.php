<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Factory;

use CoreShop\Component\Index\Factory\FilteredListingFactoryInterface;
use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class FilteredListingFactory implements FilteredListingFactoryInterface
{
    public function __construct(
        private ListingFactoryInterface $listingFactory,
    ) {
    }

    public function createList(FilterInterface $filter, ParameterBag $parameterBag): ListingInterface
    {
        $list = $this->listingFactory->createList($filter->getIndex());

        $orderKey = $filter->getOrderKey();
        $orderDirection = $filter->getOrderDirection();
        $limit = $filter->getResultsPerPage();

        if ($parameterBag->has('orderKey')) {
            $orderKey = $parameterBag->get('orderKey');
        }

        if ($parameterBag->has('order')) {
            $orderDirection = $parameterBag->get('order');
        }

        if ($parameterBag->has('perPage')) {
            $limit = $parameterBag->get('perPage');
        }

        $list->setOrderKey($orderKey);
        $list->setOrder($orderDirection);
        $list->setLimit($limit);

        //$this->filterProcessor->processConditions($filter, $list, $parameterBag);

        return $list;
    }
}
