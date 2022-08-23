<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\Variant\Model\Resolved;

use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ResolvedAttribute
{
    private AttributeInterface $attribute;
    private ArrayCollection $products;

    public function __construct(AttributeInterface $attribute = null)
    {
        $this->products = new ArrayCollection();
        if ($attribute) {
            $this->setAttribute($attribute);
        }
    }

    public function getAttribute(): AttributeInterface
    {
        return $this->attribute;
    }

    public function setAttribute(AttributeInterface $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    public function setProducts(array $products): void
    {
        $this->products = new ArrayCollection($products);
    }

    public function addProduct(ProductVariantAwareInterface $product): void
    {
        if (!$this->hasProduct($product)) {
            $this->products->add($product);
        }
    }

    public function removeProduct(ProductVariantAwareInterface $product): void
    {
        if ($this->hasProduct($product)) {
            $this->products->removeElement($product);
        }
    }

    public function hasProduct(ProductVariantAwareInterface $product): bool
    {
        return $this->products->contains($product);
    }
}