<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Extension\QueryColumnType;
use Pimcore\Model\DataObject\ClassDefinition\Data\Extension\Relation;
use Pimcore\Model\DataObject\ClassDefinition\Data\QueryResourcePersistenceAwareInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data\Relations\AbstractRelations;
use Pimcore\Model\DataObject\ClassDefinition\Data\Relations\AllowObjectRelationTrait;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\Document;
use Pimcore\Model\Element;

class DynamicDropdown extends AbstractRelations implements QueryResourcePersistenceAwareInterface
{
    use AllowObjectRelationTrait;
    use QueryColumnType;
    use Relation;

    /**
     * @var string
     */
    public $className;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopDynamicDropdown';

    /**
     * @var string
     */
    public $folderName;

    /**
     * @var string
     */
    public $methodName;

    /**
     * @var bool
     */
    public $onlyPublished;

    /**
     * Type for the column to query.
     *
     * @var array
     */
    public $queryColumnType = [
        'id' => 'int(11)',
        'type' => "enum('document','asset','object')",
    ];

    /**
     * @var string
     */
    public $recursive;

    /**
     * @var string
     */
    public $sortBy;

    /**
     * @var int
     */
    public $width;

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getFolderName()
    {
        return $this->folderName;
    }

    /**
     * @param string $folderName
     */
    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @param string $methodName
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @return bool
     */
    public function isOnlyPublished()
    {
        return $this->onlyPublished;
    }

    /**
     * @param bool $onlyPublished
     */
    public function setOnlyPublished($onlyPublished)
    {
        $this->onlyPublished = $onlyPublished;
    }

    /**
     * @return string
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param string $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param string $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return [['classes' => $this->getClassName()]];
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectsAllowed()
    {
        return true;
    }

    /**
     * @param Element\AbstractElement|null $data
     *
     * @return array
     */
    public function resolveDependencies($data)
    {
        $dependencies = [];

        if ($data instanceof Element\ElementInterface) {
            $elementType = Element\Service::getElementType($data);
            $dependencies[$elementType . '_' . $data->getId()] = [
                'id' => $data->getId(),
                'type' => $elementType
            ];
        }

        return $dependencies;
    }

    /**
     * @param Concrete|Localizedfield|Objectbrick\Data\AbstractData|Fieldcollection\Data\AbstractData $object
     * @param array $params
     *
     * @return null|Element\ElementInterface
     */
    public function preGetData($object, $params = [])
    {
        $data = null;

        if ($object instanceof Concrete) {
            $data = $object->getObjectVar($this->getName());

            if (!$object->isLazyKeyLoaded($this->getName())) {
                $data = $this->load($object);
                $object->setObjectVar($this->getName(), $data);
                $this->markLazyloadedFieldAsLoaded($object);
            }
        } elseif ($object instanceof Localizedfield) {
            $data = $params['data'];
        } elseif ($object instanceof Fieldcollection\Data\AbstractData) {
            parent::loadLazyFieldcollectionField($object);
            $data = $object->getObjectVar($this->getName());
        } elseif ($object instanceof Objectbrick\Data\AbstractData) {
            parent::loadLazyBrickField($object);
            $data = $object->getObjectVar($this->getName());
        }

        if ($data instanceof Element\ElementInterface && DataObject\AbstractObject::doHideUnpublished() &&
            !Element\Service::isPublished($data)
        ) {
            return null;
        }

        return $data;
    }

    /**
     * @param DataObject\Concrete|DataObject\Localizedfield|DataObject\Objectbrick\Data\AbstractData|DataObject\Fieldcollection\Data\AbstractData $object
     * @param array|null $data
     * @param array $params
     *
     * @return mixed
     */
    public function preSetData($object, $data, $params = [])
    {
        $this->markLazyloadedFieldAsLoaded($object);

        return $data;
    }

    /**
     * @param Element\ElementInterface $value1
     * @param Element\ElementInterface $value2
     *
     * @return bool
     */
    public function isEqual($value1, $value2)
    {
        $compareValue1 = $value1 ? $value1->getType() . $value1->getId() : null;
        $compareValue2 = $value2 ? $value2->getType() . $value2->getId() : null;

        return $compareValue1 === $compareValue2;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataForPersistence($data, $object = null, $params = [])
    {
        if (!$data instanceof Element\ElementInterface) {
            return null;
        }

        return [[
            'dest_id' => $data->getId(),
            'type' => Element\Service::getType($data),
            'fieldname' => $this->getName()
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        $queryData = [];
        $name = $this->getName();
        $rData = $this->prepareDataForPersistence($data, $object, $params);

        $queryData[$name . '__id'] = $rData[0]['dest_id'] ?? null;
        $queryData[$name . '__type'] = $rData[0]['type'] ?? null;

        return $queryData;
    }

    /**
     * {@inheritdoc}
     */
    public function loadData($data, $object = null, $params = [])
    {
        // data from relation table
        $data = is_array($data) ? $data : [];
        $data = current($data);

        $result = [
            'dirty' => false,
            'data' => null
        ];

        if (!empty($data['dest_id']) && !empty($data['type'])) {
            $element = Element\Service::getElementById($data['type'], $data['dest_id']);

            if ($element instanceof Element\ElementInterface) {
                $result['data'] = $element;
            } else {
                $result['dirty'] = true;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if (!$data instanceof Element\ElementInterface) {
            return null;
        }

        return $data->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        return DataObject::getById($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForGrid($data, $object = null, $params = [])
    {
        if (is_int($data)) {
            $data = DataObject::getById($data);
        }

        if (!$data instanceof Element\ElementInterface) {
            return null;
        }

        $method = $this->getMethodName();

        return $data->$method();
    }


     /**
     * @param mixed $value
     * @param DataObject\AbstractObject $object
     * @param mixed $params
     *
     * @return mixed
     */
    public function marshal($value, $object = null, $params = [])
    {
        if ($value) {
            $type = Element\Service::getType($value);
            $id = $value->getId();

            return [
                'type' => $type,
                'id' => $id
            ];
        }

        return null;
    }

    /**
     * @param mixed $value
     * @param DataObject\AbstractObject $object
     * @param mixed $params
     *
     * @return mixed
     */
    public function unmarshal($value, $object = null, $params = [])
    {
        if (is_array($value)) {
            $type = $value['type'];
            $id = $value['id'];

            return Element\Service::getElementById($type, $id);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        if ($data instanceof Element\AbstractElement) {
            return $data->getRealFullPath();
        }

        return '';
    }
}
