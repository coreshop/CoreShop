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

namespace CoreShop\Bundle\PimcoreBundle\Attribute;

use Attribute;

/**
 * Marks a class as a CoreShop Studio Grid Filter.
 *
 * Classes with this attribute must implement StudioGridFilterInterface
 * and will be automatically registered in the studio grid filter registry.
 *
 * Note: Filters also need the pimcore.studio_backend.search_index.data_object.filter
 * tag to actually apply the filter. This attribute only registers them in the
 * CoreShop registry for listing available filters in the API.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class AsStudioGridFilter
{
    public function __construct(
        /**
         * The service type identifier for this filter.
         * Should match the column filter type returned by getType().
         */
        public string $type,
    ) {
    }
}
