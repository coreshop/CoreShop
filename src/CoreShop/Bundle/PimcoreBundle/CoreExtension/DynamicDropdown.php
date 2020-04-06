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

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element;

class DynamicDropdown
    extends DataObject\ClassDefinition\Data\Relations\AbstractRelations
    implements DataObject\ClassDefinition\Data\QueryResourcePersistenceAwareInterface
{
    use DataObject\ClassDefinition\Data\Extension\Relation;
    use DataObject\ClassDefinition\Data\Extension\QueryColumnType;
    use DataObject\ClassDefinition\Data\Relations\AllowObjectRelationTrait;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopDynamicDropdown';

    /**
     * Type for the column to query
     *
     * @var array
     */
    public $queryColumnType = [
        'id' => 'int(11)',
        'type' => "enum('document','asset','object')",
    ];

    /**
     * @var int
     */
    public $width;

    /**
     * @var string
     */
    public $folderName;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $methodName;

    /**
     * @var string
     */
    public $recursive;

    /**
     * @var string
     */
    public $sortBy;

    /**
     * @var bool
     */
    public $onlyPublished;

    /**
     * {@inheritdoc}
     */
    public function getObjectsAllowed()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return [['classes' => $this->getClassName()]];
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
     * @return mixed
     */
    public function getFolderName()
    {
        return $this->folderName;
    }

    /**
     * @param mixed $folderName
     */
    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @param mixed $methodName
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @return mixed
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param mixed $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
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
     * @param DataObject\Concrete|DataObject\Localizedfield|DataObject\Objectbrick\Data\AbstractData|DataObject\Fieldcollection\Data\AbstractData $object
     * @param array $params
     *
     * @return null|Element\ElementInterface
     */
    public function preGetData($object, $params = [])
    {
        $data = null;

        if ($object instanceof DataObject\Concrete) {
            $data = $object->getObjectVar($this->getName());

            if (!$object->isLazyKeyLoaded($this->getName())) {
                $data = $this->load($object);

                $object->setObjectVar($this->getName(), $data);
                $this->markLazyloadedFieldAsLoaded($object);
            }
        } elseif ($object instanceof DataObject\Localizedfield) {
            $data = $params['data'];
        } elseif ($object instanceof DataObject\Fieldcollection\Data\AbstractData) {
            parent::loadLazyFieldcollectionField($object);
            $data = $object->getObjectVar($this->getName());
        } elseif ($object instanceof DataObject\Objectbrick\Data\AbstractData) {
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
     * @inheritdoc
     */
    public function prepareDataForPersistence($data, $object = null, $params = [])
    {
        if ($data instanceof Element\ElementInterface) {
            $type = Element\Service::getType($data);
            $id = $data->getId();

            return [[
                'dest_id' => $id,
                'type' => $type,
                'fieldname' => $this->getName()
            ]];
        }

        return null;
    }

    /**
     * @see QueryResourcePersistenceAwareInterface::getDataForQueryResource
     *
     * @param Asset|Document|DataObject\AbstractObject $data
     * @param null|DataObject\AbstractObject $object
     * @param mixed $params
     *
     * @return array
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        $rData = $this->prepareDataForPersistence($data, $object, $params);
        $return = [];

        $return[$this->getName() . '__id'] = $rData[0]['dest_id'] ?? null;
        $return[$this->getName() . '__type'] = $rData[0]['type'] ?? null;

        return $return;
    }

    /**
     * @inheritdoc
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
     *
     * @return int|null
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if ($data) {
            return $data->getId();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = array())
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

        if ($data instanceof Element\ElementInterface) {
            $method = $this->getMethodName();

            return $data->$method();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
    {
    }
}
