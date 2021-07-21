<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ProductBundle\CoreExtension;

use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;

class ProductUnitDefinition extends Data implements
    Data\ResourcePersistenceAwareInterface,
    Data\QueryResourcePersistenceAwareInterface,
    Data\CustomVersionMarshalInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductUnitDefinition';

    /**
     * Type for the generated phpdoc.
     *
     * @var string
     */
    public $phpdocType = '\\' . ProductUnitDefinitionInterface::class;

    /**
     * @var bool
     */
    public $allowEmpty = false;

    public function getParameterTypeDeclaration(): ?string
    {
        return '?\\' . ProductUnitDefinitionInterface::class;
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return '?\\' . ProductUnitDefinitionInterface::class;
    }

    public function getPhpdocInputType(): ?string
    {
        return '\\' . ProductUnitDefinitionInterface::class;
    }

    public function getPhpdocReturnType(): ?string
    {
        return '\\' . ProductUnitDefinitionInterface::class;
    }

    /**
     * @return string | array
     */
    public function getQueryColumnType()
    {
        return 'int(11)';
    }

    /**
     * @return string | array
     */
    public function getColumnType()
    {
        return 'int(11)';
    }

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    public function preSetData($object, $data, $params = [])
    {
        if (is_int($data) || is_string($data)) {
            if ((int) $data) {
                return $this->getDataFromResource($data, $object, $params);
            }
        }

        return $data;
    }

    public function preGetData($object, $params = [])
    {
        /**
         * @var Concrete $object
         */
        $data = $object->getObjectVar($this->getName());

        if ($data instanceof ResourceInterface && $data->getId()) {
            //Reload from Database, but only if available
            $tmpData = $this->getRepository()->find($data->getId());

            if ($tmpData instanceof ResourceInterface) {
                //Dirty Fix, Pimcore sometimes calls properties without getter
                //This could cause Problems with translations, therefore, we need to set
                //the value here
                $object->setValue($this->getName(), $tmpData);

                return $tmpData;
            }
        }

        return $data;
    }

    public function getDataForResource($data, $object = null, $params = [])
    {
        if ($data instanceof ProductUnitDefinitionInterface) {
            return $data->getId();
        }

        return null;
    }

    public function getDataFromResource($data, $object = null, $params = [])
    {
        if ((int) $data > 0) {
            return $this->getRepository()->find($data);
        }

        return null;
    }

    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        if ($data instanceof ProductUnitDefinitionInterface) {
            return $data->getId();
        }

        return null;
    }

    public function marshalVersion($object, $data)
    {
        return $this->getDataForEditmode($data, $object);
    }

    public function unmarshalVersion($object, $data)
    {
        return $this->getDataFromEditmode($data, $object);
    }

    public function marshalRecycleData($object, $data)
    {
        return $this->marshalVersion($object, $data);
    }

    public function unmarshalRecycleData($object, $data)
    {
        return $this->unmarshalVersion($object, $data);
    }

    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $parsedData = [
            'id' => null,
            'conversationRate' => null,
            'precision' => null,
            'unitName' => null,
        ];

        if ($data instanceof ProductUnitDefinitionInterface) {
            $parsedData = [
                'id' => $data->getId(),
                'conversationRate' => $data->getConversionRate(),
                'precision' => $data->getPrecision(),
                'unitName' => $data->getUnit()->getName(),
                'fullLabel' => $data->getUnit()->getFullLabel(),
                'fullPluralLabel' => $data->getUnit()->getFullPluralLabel(),
                'shortLabel' => $data->getUnit()->getShortLabel(),
                'shortPluralLabel' => $data->getUnit()->getShortPluralLabel(),
            ];
        }

        return $parsedData;
    }

    public function isEmpty($data)
    {
        return !$data instanceof ProductUnitDefinitionInterface;
    }

    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $data;
    }

    public function getForCsvExport($object, $params = [])
    {
        return '';
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_unit_definition');
    }
}
