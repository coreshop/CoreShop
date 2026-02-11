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

/**
 * Interface for CoreShop grid filters in Pimcore Studio v2.
 *
 * Provides metadata methods for listing available filters in the UI.
 * Filter classes should also implement Pimcore's FilterInterface directly
 * for the actual filter application.
 */
interface StudioGridFilterInterface
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
