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

namespace CoreShop\Component\Variant;

use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use CoreShop\Component\Variant\Model\Resolved\ResolvedAttributeGroup;

interface AttributeCollectorInterface
{
    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributesFromVariants(ProductVariantAwareInterface $product, bool $showInList = true): array;

    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributesFromObject(ProductVariantAwareInterface $product, bool $showInList = true): array;

    /**
     * @return ResolvedAttributeGroup[]
     */
    public function getAttributes(array $products, bool $showInList = true): array;

    public function getIndex(ProductVariantAwareInterface $product);
}
