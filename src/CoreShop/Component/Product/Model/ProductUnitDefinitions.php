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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
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

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getDefaultUnitDefinition()
    {
        return $this->defaultUnitDefinition;
    }

    public function setDefaultUnitDefinition(ProductUnitDefinitionInterface $defaultUnitDefinition)
    {
        $defaultUnitDefinition->setConversionRate(1.0);
        $this->addUnitDefinition($defaultUnitDefinition);
        $this->defaultUnitDefinition = $this->getUnitDefinition($defaultUnitDefinition->getUnitName());
    }

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

    public function hasUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        return $this->unitDefinitions->contains($unitDefinition);
    }

    public function removeUnitDefinition(ProductUnitDefinitionInterface $productUnitDefinition)
    {
        if ($this->unitDefinitions->contains($productUnitDefinition)) {
            $this->unitDefinitions->removeElement($productUnitDefinition);
        }
    }

    public function getUnitDefinitions()
    {
        return $this->unitDefinitions;
    }

    public function getUnitDefinition(?string $identifier)
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

    public function addAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        $productUnit = $unitDefinition->getUnit();
        $defaultDefinition = $this->getDefaultUnitDefinition();

        $defaultDefinitionUnit = $defaultDefinition;
        if ($productUnit === $defaultDefinitionUnit) {
            return;
        }

        $this->addUnitDefinition($unitDefinition);
    }

    public function removeAdditionalUnitDefinition(ProductUnitDefinitionInterface $unitDefinition)
    {
        $productUnit = $unitDefinition->getUnit();
        $defaultDefinition = $this->getDefaultUnitDefinition();

        $defaultDefinitionUnit = $defaultDefinition;
        if ($productUnit === $defaultDefinitionUnit) {
            return;
        }

        $this->removeUnitDefinition($unitDefinition);
    }

    public function getAdditionalUnitDefinitions()
    {
        $defaultDefinition = $this->getDefaultUnitDefinition();

        if (null === $defaultDefinition) {
            return new ArrayCollection();
        }

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

        return new ArrayCollection(array_values($additionalDefinitions->toArray()));
    }
}
