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

declare(strict_types=1);

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
