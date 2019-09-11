<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Store\Model\StoreAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductStoreValues extends AbstractResource implements ProductStoreValuesInterface
{
    use StoreAwareTrait;

    /**
     * @var int
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

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return (int)$this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function addProductUnitDefinitionPrice(ProductUnitDefinitionPriceInterface $productUnitDefinitionPrice)
    {
        if (!$this->productUnitDefinitionPrices->contains($productUnitDefinitionPrice)) {
            $productUnitDefinitionPrice->setProductStoreValues($this);
            $this->productUnitDefinitionPrices->add($productUnitDefinitionPrice);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductUnitDefinitionPrice(ProductUnitDefinitionPriceInterface $productUnitDefinitionPrice)
    {
        if ($this->productUnitDefinitionPrices->contains($productUnitDefinitionPrice)) {
            $this->productUnitDefinitionPrices->removeElement($productUnitDefinitionPrice);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductUnitDefinitionPrices()
    {
        return $this->productUnitDefinitionPrices;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
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
}
