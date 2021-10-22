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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
abstract class Select extends Data implements
    Data\ResourcePersistenceAwareInterface,
    Data\QueryResourcePersistenceAwareInterface,
    Data\CustomRecyclingMarshalInterface
{
    use Model\DataObject\Traits\SimpleComparisonTrait;

    /**
     * @var bool
     */
    public $allowEmpty = false;

    /**
     * @return RepositoryInterface
     */
    abstract protected function getRepository();

    abstract protected function getModel(): string;

    abstract protected function getInterface(): string;

    abstract protected function getNullable(): bool;

    public function getParameterTypeDeclaration(): ?string
    {
        return ($this->getNullable() ? '?' : '') . $this->getInterface();
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return ($this->getNullable() ? '?' : '') . $this->getInterface();
    }

    public function getPhpdocInputType(): ?string
    {
        return ($this->getNullable() ? 'null|' : '') . $this->getInterface();
    }

    public function getPhpdocReturnType(): ?string
    {
        return ($this->getNullable() ? 'null|' : '') . $this->getInterface();
    }

    public function marshalVersion($object, $data)
    {
        if ($data instanceof ResourceInterface) {
            return $data->getId();
        }

        return $data;
    }

    public function unmarshalVersion($object, $data)
    {
        if (null === $data) {
            return null;
        }

        return $this->getRepository()->find($data);
    }

    public function marshalRecycleData($object, $data)
    {
        return $this->marshalVersion($object, $data);
    }

    public function unmarshalRecycleData($object, $data)
    {
        return $this->unmarshalVersion($object, $data);
    }

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    public function getQueryColumnType()
    {
        return 'int(11)';
    }

    public function getColumnType()
    {
        return 'int(11)';
    }

    public function preSetData($object, $data, $params = [])
    {
        if (is_int($data) || is_string($data)) {
            if ((int)$data) {
                return $this->getDataFromResource($data, $object, $params);
            }
        }

        return $data;
    }

    public function preGetData($object, $params = [])
    {
        if (!$object instanceof Model\AbstractModel) {
            return null;
        }

        $data = $object->getObjectVar($this->getName());

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
     * @param string $data
     * @param null   $object
     * @param array  $params
     *
     * @return int|string|null
     */
    public function getDataForResource($data, $object = null, $params = [])
    {
        if ($data !== null && method_exists($data, 'getId') && is_a($data, $this->getModel())) {
            return $data->getId();
        }

        return null;
    }

    /**
     * @param string $data
     * @param null   $object
     * @param array  $params
     *
     * @return ResourceInterface|object|null
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        if ((int)$data > 0) {
            return $this->getRepository()->find($data);
        }

        return null;
    }

    /**
     * @param string $data
     * @param null   $object
     * @param array  $params
     *
     * @return int|null
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        if ($data !== null && method_exists($data, 'getId') && is_a($data, $this->getModel())) {
            return $data->getId();
        }

        return null;
    }

    public function getDataForEditmode($data, $object = null, $params = [])
    {
        return $this->getDataForResource($data, $object, $params);
    }

    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return $this->getDataFromResource($data, $object, $params);
    }

    public function isEmpty($data)
    {
        return !$data;
    }

    public function getDataForSearchIndex($object, $params = [])
    {
        if ($object instanceof ResourceInterface) {
            return $object->getId();
        }

        return parent::getDataForSearchIndex($object, $params);
    }

    public function isAllowEmpty()
    {
        return $this->allowEmpty;
    }

    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }
}
