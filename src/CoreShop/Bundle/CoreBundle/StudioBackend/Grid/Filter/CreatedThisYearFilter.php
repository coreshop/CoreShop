<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\StudioBackend\Grid\Filter;

use Carbon\Carbon;
use CoreShop\Component\Pimcore\DataObject\Grid\StudioGridFilterInterface;
use Pimcore\Bundle\StudioBackendBundle\DataIndex\Filter\FilterInterface;
use Pimcore\Bundle\StudioBackendBundle\DataIndex\Query\QueryInterface;
use Pimcore\Bundle\StudioBackendBundle\MappedParameter\Filter\ColumnFiltersParameterInterface;

final class CreatedThisYearFilter implements FilterInterface, StudioGridFilterInterface
{
    public const COLUMN_TYPE = 'coreshop_created_this_year';

    public function getType(): string
    {
        return self::COLUMN_TYPE;
    }

    public function getLabel(): string
    {
        return 'coreshop_grid_filter_created_this_year';
    }

    public function apply(mixed $parameters, QueryInterface $query): QueryInterface
    {
        if (!$parameters instanceof ColumnFiltersParameterInterface) {
            return $query;
        }

        $isRequested = false;
        foreach ($parameters->getColumnFilters() as $filter) {
            if (($filter['type'] ?? null) === self::COLUMN_TYPE) {
                $isRequested = true;
                break;
            }
        }

        if (!$isRequested) {
            return $query;
        }

        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear()->addDay();

        return $query->filterDatetime(
            'system_fields.creationDate',
            $startOfYear,
            $endOfYear,
            null,
            true,
            false,
        );
    }

    public function supports(string $listType): bool
    {
        return in_array($listType, [
            'coreshop_order',
            'coreshop_cart',
            'coreshop_quote',
        ], true);
    }
}
