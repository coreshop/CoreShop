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

namespace CoreShop\Bundle\ProductBundle\CoreExtension;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;

class ProductUnitDefinition extends Data
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

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        if (is_int($data) || is_string($data)) {
            if ((int) $data) {
                return $this->getDataFromResource($data, $object, $params);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preGetData($object, $params = [])
    {
        //TODO: Remove once CoreShop requires min Pimcore 5.5
        if (method_exists($object, 'getObjectVar')) {
            $data = $object->getObjectVar($this->getName());
        } else {
            $data = $object->{$this->getName()};
        }

        if ($data instanceof ResourceInterface) {
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

    /**
     * {@inheritdoc}
     */
    public function getDataForResource($data, $object = null, $params = [])
    {
        if (is_a($data, $this->getModel())) {
            return $data->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        if ((int) $data > 0) {
            return $this->getRepository()->find($data);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        if (is_a($data, $this->getModel())) {
            return $data->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $parsedData = [
            'id'               => null,
            'conversationRate' => null,
            'unitName'         => null
        ];

        if ($data instanceof ProductUnitDefinitionInterface) {
            $parsedData = [
                'id'               => $data->getId(),
                'conversationRate' => $data->getConversionRate(),
                'unitName'         => $data->getUnit()->getName()
            ];
        }

        return $parsedData;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty($data)
    {
        return !$data instanceof ProductUnitDefinitionInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_unit_definition');
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.product_unit_definition.class');
    }
}
