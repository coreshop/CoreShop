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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductUnitDefinitions extends AbstractResource implements ProductUnitDefinitionsInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ProductUnitDefinitionInterface
     */
    protected $defaultUnitDefinition;

    /**
     * @var Collection|ProductUnitDefinitionInterface[]
     */
    protected $unitDefinitions;

    public function __construct()
    {
        $this->unitDefinitions = new ArrayCollection();
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
     * {@inheritdoc}
     */
    public function getDefaultUnitDefinition()
    {
        return $this->defaultUnitDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultUnitDefinition(ProductUnitDefinitionInterface $defaultUnitDefinition)
    {
        if ($defaultUnitDefinition) {
            $defaultUnitDefinition->setConversionRate(1.0);
            $this->addUnitDefinition($defaultUnitDefinition);
            $this->defaultUnitDefinition = $this->getUnitDefinition($defaultUnitDefinition->getUnitName());
        } else {
            $this->defaultUnitDefinition = $defaultUnitDefinition;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition)
    {
        $productUnit = $productUnitDefinition->getUnit();

        if ($productUnit instanceof ProductUnitInterface &&
            $existingUnitDefinition = $this->getUnitDefinition($productUnit->getName())
        ) {
            $existingUnitDefinition->setPrecision($productUnitDefinition->getPrecision());
            $existingUnitDefinition->setConversionRate($productUnitDefinition->getConversionRate());
            $existingUnitDefinition->setProductUnitDefinitions($this);
        } else {
            $productUnitDefinition->setProductUnitDefinitions($this);
            $this->unitDefinitions->add($productUnitDefinition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition)
    {
        if ($this->unitDefinitions->contains($productUnitDefinition)) {
            $this->unitDefinitions->removeElement($productUnitDefinition);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitDefinitions()
    {
        return $this->unitDefinitions;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitDefinition(string $identifier)
    {
        $result = null;

        foreach ($this->unitDefinitions as $unitDefinition) {
            if ($unit = $unitDefinition->getUnit()) {
                if ($unit->getName() === $identifier) {
                    $result = $unitDefinition;

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        $productUnit = $unitDefinition->getUnit();
        $defaultDefinition = $this->getDefaultUnitDefinition();

        $defaultDefinitionUnit = $defaultDefinition ? $defaultDefinition->getUnit() : null;
        if ($productUnit === $defaultDefinitionUnit) {
            return;
        }

        $this->addUnitDefinition($unitDefinition);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        $productUnit = $unitDefinition->getUnit();
        $defaultDefinition = $this->getDefaultUnitDefinition();

        $defaultDefinitionUnit = $defaultDefinition ? $defaultDefinition->getUnit() : null;
        if ($productUnit === $defaultDefinitionUnit) {
            return;
        }

        $this->removeUnitDefinition($unitDefinition);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalUnitDefinitions()
    {
        $defaultDefinition = $this->getDefaultUnitDefinition();
        $additionalDefinitions = $this->getUnitDefinitions()
            ->filter(function ($precision) use ($defaultDefinition) {
                return $precision !== $defaultDefinition;
            });

        $additionalDefinitionsSorted = new ArrayCollection(array_values($additionalDefinitions->toArray()));

        return $additionalDefinitionsSorted;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $defaultUnit = $this->getDefaultUnitDefinition() instanceof ProductUnitDefinitionInterface
        && $this->getDefaultUnitDefinition()->getUnit() instanceof ProductUnitInterface ? $this->getDefaultUnitDefinition()->getUnit()->getName() : '--';

        return sprintf('Default Unit: %s, additional units: %d', $defaultUnit, $this->getAdditionalUnitDefinitions()->count());
    }
}
