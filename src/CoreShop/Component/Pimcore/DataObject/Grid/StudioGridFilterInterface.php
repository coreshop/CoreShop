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

namespace CoreShop\Component\Pimcore\DataObject\Grid;

use Pimcore\Bundle\StudioBackendBundle\DataIndex\Filter\FilterInterface;

/**
 * Interface for CoreShop grid filters in Pimcore Studio v2.
 *
 * Extends Pimcore's FilterInterface and adds metadata methods for
 * listing available filters in the UI.
 *
 * Each filter has its own column type and registers directly with
 * Pimcore's filter system (pimcore.studio_backend.search_index.data_object.filter).
 */
interface StudioGridFilterInterface extends FilterInterface
{
    /**
     * Get the column filter type for this filter.
     *
     * This is the value that must be sent as 'type' in the columnFilter
     * from the frontend (e.g., 'coreshop_created_today').
     */
    public function getType(): string;

    /**
     * Get a human-readable label for the filter (translation key).
     */
    public function getLabel(): string;

    /**
     * Check if this filter supports the given list type.
     *
     * @param string $listType The list type (e.g., 'coreshop_order', 'coreshop_cart')
     */
    public function supports(string $listType): bool;
}
