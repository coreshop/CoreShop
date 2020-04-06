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
use Pimcore\Model\Element\Service;

class DynamicDropdownMultiple extends
    DataObject\ClassDefinition\Data\Relations\AbstractRelations
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
    public $fieldtype = 'coreShopDynamicDropdownMultiple';

    /**
     * Type for the column to query
     *
     * @var string
     */
    public $queryColumnType = 'text';

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
     * @return bool
     */
    public function getObjectsAllowed()
    {
        return true;
    }

    /**
     * @param DataObject\Concrete|DataObject\Localizedfield|DataObject\Objectbrick\Data\AbstractData|DataObject\Fieldcollection\Data\AbstractData $object
     * @param array $params
     *
     * @return array
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

        if (is_array($data) && DataObject\AbstractObject::doHideUnpublished()) {
            $publishedList = [];

            foreach ($data as $listElement) {
                if (Service::isPublished($listElement)) {
                    $publishedList[] = $listElement;
                }
            }

            return $publishedList;
        }

        return is_array($data) ? $data : [];
    }

    /**
     * @see Data::getDataFromEditmode
     *
     * @param array $data
     * @param null|DataObject\AbstractObject $object
     * @param mixed $params
     *
     * @return array
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if ($data === null || $data === false) {
            return [];
        }

        $objects = [];
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $ob) {
                $o = DataObject::getById($ob['id']);

                if ($o) {
                    $objects[] = $o;
                }
            }
        }

        // must return array if data shall be set
        return $objects;
    }

    /**
     * @see Data::getDataForEditmode
     *
     * @param array $data
     * @param null|DataObject\AbstractObject $object
     * @param mixed $params
     *
     * @return array
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $return = [];

        // add data
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $referencedObject) {
                if ($referencedObject instanceof DataObject\Concrete) {
                    $return[] = $referencedObject->getId();
                }
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataForPersistence($data, $object = null, $params = [])
    {
        $return = [];

        if (is_array($data)) {
            if (count($data) > 0) {
                $counter = 1;

                foreach ($data as $obj) {
                    if ($obj instanceof DataObject\Concrete) {
                        $return[] = [
                            'dest_id' => $obj->getId(),
                            'type' => 'object',
                            'fieldname' => $this->getName(),
                            'index' => $counter
                        ];
                    }

                    $counter++;
                }

                return $return;
            }

            if (count($data) === 0) {
                return [];
            }
        }

        // return null if data was null - this indicates data was not loaded
        return null;
    }

    /**
     * @see QueryResourcePersistenceAwareInterface::getDataForQueryResource
     *
     * @param array $data
     * @param null|DataObject\AbstractObject $object
     * @param mixed $params
     *
     * @throws \Exception
     *
     * @return string|null
     */
    public function getDataForQueryResource($data, $object = null, $params = [])
    {
        // return null when data is not set
        if (!$data) {
            return null;
        }

        $ids = [];

        if (is_array($data)) {
            if (count($data) > 0) {
                foreach ($data as $obj) {
                    if ($obj instanceof DataObject\Concrete) {
                        $ids[] = $obj->getId();
                    }
                }

                return ',' . implode(',', $ids) . ',';
            }

            if (count($data) === 0) {
                return '';
            }
        }

        throw new \Exception('invalid data passed to getDataForQueryResource - must be array and it is: ' . print_r($data, true));
    }

    /**
     * @inheritdoc
     */
    public function loadData($data, $object = null, $params = [])
    {
        $objects = [
            'dirty' => false,
            'data' => []
        ];

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $obj) {
                $o = DataObject::getById($obj['dest_id']);
                if ($o instanceof DataObject\Concrete) {
                    $objects['data'][] = $o;
                } else {
                    $objects['dirty'] = true;
                }
            }
        }

        // must return array - otherwise this means data is not loaded
        return $objects;
    }
}
