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
use Pimcore\Model\Element;
use RuntimeException;

class DynamicDropdownMultiple extends AbstractRelations implements QueryResourcePersistenceAwareInterface
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
    public $fieldtype = 'coreShopDynamicDropdownMultiple';

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
     * @var string
     */
    public $queryColumnType = 'text';

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
     * @param Element\AbstractElement[]|null $data
     *
     * @return array
     */
    public function resolveDependencies($data)
    {
        $dependencies = [];

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $e) {
                if ($e instanceof Element\ElementInterface) {
                    $elementType = Element\Service::getElementType($e);
                    $dependencies[$elementType . '_' . $e->getId()] = [
                        'id' => $e->getId(),
                        'type' => $elementType
                    ];
                }
            }
        }

        return $dependencies;
    }

    /**
     * @param Concrete|Localizedfield|Objectbrick\Data\AbstractData|Fieldcollection\Data\AbstractData $object
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
                if (Element\Service::isPublished($listElement)) {
                    $publishedList[] = $listElement;
                }
            }

            return $publishedList;
        }

        return is_array($data) ? $data : [];
    }

    /**
     * @param DataObject\Concrete|DataObject\Localizedfield|DataObject\Objectbrick\Data\AbstractData|DataObject\Fieldcollection\Data\AbstractData $object
     * @param array|null $data
     * @param array $params
     *
     * @return array|null
     */
    public function preSetData($object, $data, $params = [])
    {
        if ($data === null) {
            $data = [];
        }

        $this->markLazyloadedFieldAsLoaded($object);

        return $data;
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
        if (is_array($value)) {
            $result = [];
            foreach ($value as $element) {
                $type = Element\Service::getType($element);
                $id = $element->getId();
                $result[] = [
                    'type' => $type,
                    'id' => $id
                ];
            }

            return $result;
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
            $result = [];
            foreach ($value as $elementData) {
                $type = $elementData['type'];
                $id = $elementData['id'];
                $element = Element\Service::getElementById($type, $id);
                if ($element) {
                    $result[] = $element;
                }
            }

            return $result;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if ($data === null || $data === false) {
            return [];
        }

        $objects = [];

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $objectId) {
                $obj = DataObject::getById($objectId);

                if (!$obj instanceof DataObject\Concrete) {
                    continue;
                }

                $objects[] = $obj;
            }
        }

        // must return array if data shall be set
        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $return = [];

        // add data
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $referencedObject) {
                if (!$referencedObject instanceof DataObject\Concrete) {
                    continue;
                }

                $return[] = $referencedObject->getId();
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
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
                            'index' => $counter,
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
     * {@inheritdoc}
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
                    if (!$obj instanceof DataObject\Concrete) {
                        continue;
                    }

                    $ids[] = $obj->getId();
                }

                return sprintf(',%s,', implode(',', $ids));
            }

            if (count($data) === 0) {
                return '';
            }
        }

        throw new RuntimeException(
            sprintf(
                'Invalid data passed to getDataForQueryResource - must be array and it is: %s',
                gettype($data)
            )
        );
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
