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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductUnitDefinitions extends AbstractResource implements ProductUnitDefinitionsInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ProductUnitDefinitionInterface|null
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
    public function hasUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition)
    {
        return $this->unitDefinitions->contains($productUnitDefinition);
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

        if (null === $defaultDefinition->getUnit()) {
            return new ArrayCollection();
        }

        $additionalDefinitions = $this->getUnitDefinitions()
            ->filter(function (ProductUnitDefinitionInterface $definition) use ($defaultDefinition) {
                if (null === $definition->getUnit()) {
                    return false;
                }

                return $definition->getUnit()->getId() !== $defaultDefinition->getUnit()->getId();
            });

        $additionalDefinitionsSorted = new ArrayCollection(array_values($additionalDefinitions->toArray()));

        return $additionalDefinitionsSorted;
    }

    public function __clone()
    {
        if ($this->id === null) {
            return;
        }

        $newDefaultUnitDefinition = clone $this->getDefaultUnitDefinition();
        $newDefaultUnitDefinition->setProductUnitDefinitions($this);

        $additionalUnits = $this->getAdditionalUnitDefinitions();

        $this->id = null;
        $this->unitDefinitions =  new ArrayCollection();
        $this->defaultUnitDefinition = null;

        $this->setDefaultUnitDefinition($newDefaultUnitDefinition);

        if ($additionalUnits instanceof Collection) {
            foreach ($additionalUnits as $additionalUnit) {
                $newAdditionalDefinition = clone $additionalUnit;
                $newAdditionalDefinition->setProductUnitDefinitions($this);
                $this->addUnitDefinition($newAdditionalDefinition);
            }
        }
    }
}
