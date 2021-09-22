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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductStoreValues extends AbstractResource implements ProductStoreValuesInterface
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
     * @var Collection|ProductUnitDefinitionPriceInterface[]
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

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    public function addProductUnitDefinitionPrice(ProductUnitDefinitionPriceInterface $productUnitDefinitionPrice)
    {
        if (!$this->productUnitDefinitionPrices->contains($productUnitDefinitionPrice)) {
            $productUnitDefinitionPrice->setProductStoreValues($this);
            $this->productUnitDefinitionPrices->add($productUnitDefinitionPrice);
        }
    }

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

    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Price: %s (Store: %d)', $this->getPrice(), $this->getStore()->getId());
    }

    public function __clone()
    {
        $this->id = null;
    }
}
