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
 * Default behaviour: every sort name is an index column (filtered listing) or a product listing column.
 */
final class CategorySortApplier implements CategorySortApplierInterface
{
    public function getSortOptions(): array
    {
        return [];
    }

    public function applyToIndexListing(ListingInterface $list, string $name, string $direction): void
    {
        $list->setOrderKey($name);
        $list->setOrder($direction);
    }

    public function applyToProductListingOptions(array $options, string $name, string $direction): array
    {
        $options['order_key'] = $name;
        $options['order'] = $direction;

        return $options;
    }
}
