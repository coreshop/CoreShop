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

use CoreShop\Component\Product\Model\ProductAdditionalUnitInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
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
    protected $price;

    /**
     * @var ProductUnitInterface
     */
    protected $defaultUnit;

    /**
     * @var int
     */
    protected $defaultUnitPrecision;

    /**
     * @var Collection|ProductAdditionalUnitInterface[]
     */
    protected $additionalUnits;

    /**
     * @var ProductInterface
     */
    protected $product;

    public function __construct()
    {
        $this->additionalUnits = new ArrayCollection();
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
        return $this->price;
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
    public function getDefaultUnit()
    {
        return $this->defaultUnit;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultUnit(ProductUnitInterface $unit)
    {
        $this->defaultUnit = $unit;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUnitPrecision()
    {
        return $this->defaultUnitPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultUnitPrecision(int $defaultUnitPrecision)
    {
        $this->defaultUnitPrecision = $defaultUnitPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdditionalUnit(ProductAdditionalUnitInterface $productAdditionalUnit)
    {
        $productUnit = $productAdditionalUnit->getUnit();
        if ($productUnit instanceof ProductUnitInterface &&
            $existingAdditionalUnit = $this->getAdditionalUnit($productUnit->getIdentifier())
        ) {
            $existingAdditionalUnit->setPrecision($productAdditionalUnit->getPrecision());
            $existingAdditionalUnit->setConversionRate($productAdditionalUnit->getConversionRate());
            $existingAdditionalUnit->setProduct($this->getProduct());
        } else {
            $productAdditionalUnit->setProduct($this->getProduct());
            $this->additionalUnits->add($productAdditionalUnit);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdditionalUnit(ProductAdditionalUnitInterface $productAdditionalUnit)
    {
        if ($this->additionalUnits->contains($productAdditionalUnit)) {
            $this->additionalUnits->removeElement($productAdditionalUnit);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalUnits()
    {
        return $this->additionalUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalUnit(string $identifier)
    {
        $result = null;

        foreach ($this->additionalUnits as $unitPrecision) {
            if ($unit = $unitPrecision->getUnit()) {
                if ($unit->getIdentifier() === $identifier) {
                    $result = $unitPrecision;
                    break;
                }
            }
        }

        return $result;
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
        $defaultUnit = $this->getDefaultUnit() instanceof ProductUnitInterface ? $this->getDefaultUnit()->getId() : '--';
        return sprintf('Price: %s, Default Unit: %s, Precision: %d', $this->getPrice(), $defaultUnit, $this->getDefaultUnitPrecision());
    }
}
