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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductStoreValues extends AbstractResource implements ProductStoreValuesInterface, \Stringable
{
    use StoreAwareTrait;

    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int
     */
    protected $price = 0;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var Collection<int, ProductUnitDefinitionPriceInterface>|ProductUnitDefinitionPriceInterface[]
     */
    protected $productUnitDefinitionPrices;

    public function __construct()
    {
        $this->productUnitDefinitionPrices = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return void
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * @return void
     */
    public function addProductUnitDefinitionPrice(ProductUnitDefinitionPriceInterface $productUnitDefinitionPrice)
    {
        if (!$this->productUnitDefinitionPrices->contains($productUnitDefinitionPrice)) {
            $productUnitDefinitionPrice->setProductStoreValues($this);
            $this->productUnitDefinitionPrices->add($productUnitDefinitionPrice);
        }
    }

    /**
     * @return void
     */
    public function removeProductUnitDefinitionPrice(ProductUnitDefinitionPriceInterface $productUnitDefinitionPrice)
    {
        if ($this->productUnitDefinitionPrices->contains($productUnitDefinitionPrice)) {
            $this->productUnitDefinitionPrices->removeElement($productUnitDefinitionPrice);
        }
    }

    public function getProductUnitDefinitionPrices()
    {
        return $this->productUnitDefinitionPrices;
    }

    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return void
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function __toString(): string
    {
        return sprintf('Price: %s (Store: %d)', $this->getPrice(), $this->getStore()->getId());
    }
}
