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
    public const EMPTY_STRING = '##EMPTY##';

    public function addCondition(
        FilterConditionInterface $condition,
        FilterInterface $filter,
        ListingInterface $list,
        array $currentFilter,
        ParameterBag $parameterBag,
        bool $isPrecondition = false,
    ): array;

    public function prepareValuesForRendering(
        FilterConditionInterface $condition,
        FilterInterface $filter,
        ListingInterface $list,
        array $currentFilter,
    ): array;
}
