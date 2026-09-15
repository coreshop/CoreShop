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

namespace CoreShop\Bundle\FrontendBundle\Listing;

use CoreShop\Component\Index\Listing\ListingInterface;

/**
 * Applies the "<name>_<direction>" sort chosen on a category page to the listing. Decorate it to add
 * sort options that are not plain index columns (a price index, popularity, ...): return the extra
 * names from getSortOptions() and handle them in the two apply methods, delegate everything else.
 */
interface CategorySortApplierInterface
{
    /**
     * Sort names offered and accepted in addition to core_shop_frontend.category.valid_sort_options.
     *
     * @return string[]
     */
    public function getSortOptions(): array;

    /**
     * Sorts the index listing of a category with a filter.
     */
    public function applyToIndexListing(ListingInterface $list, string $name, string $direction): void;

    /**
     * Returns the options for ProductRepositoryInterface::getProductsListing() of a category without a filter,
     * with the sort applied (order_key, order and optionally order_key_quote).
     */
    public function applyToProductListingOptions(array $options, string $name, string $direction): array;
}
