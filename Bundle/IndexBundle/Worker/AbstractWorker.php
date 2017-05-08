<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Getter\GetterInterface;
use CoreShop\Component\Index\Interpreter\InterpreterInterface;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Logger;
use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\Concrete;
use Pimcore\Tool;

abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $getterServiceRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $interpreterServiceRegistry;

    public function __construct(
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry
    ) {
        $this->getterServiceRegistry = $getterServiceRegistry;
        $this->interpreterServiceRegistry = $interpreterServiceRegistry;
    }

    /**
     * prepares Data for index.
     *
     * @param IndexInterface        $index
     * @param PimcoreModelInterface $object
     * @param bool                  $convertArrayToString
     *
     * @return array
     */
    protected function prepareData(IndexInterface $index, PimcoreModelInterface $object, $convertArrayToString = true)
    {
        $a = \Pimcore::inAdmin();
        $b = AbstractObject::doGetInheritedValues();
        \Pimcore::unsetAdminMode();
        AbstractObject::setGetInheritedValues(true);
        $hidePublishedMemory = AbstractObject::doHideUnpublished();
        AbstractObject::setHideUnpublished(false);

        $categories = [];
        $categoryIds = [];
        $parentCategoryIds = [];

        //TODO: Should be refactored?
        if (method_exists($object, 'getCategories')) {
            $categories = $object->getCategories();

            if ($categories) {
                foreach ($categories as $c) {
                    $categoryIds[$c->getId()] = $c->getId();

                    if (method_exists($c, 'getHierarchy')) {
                        $parents = $c->getHierarchy();

                        foreach ($parents as $p) {
                            $parentCategoryIds[] = $p->getId();
                        }
                    }
                }
            }
        }

        ksort($categoryIds);
        $categoryIds = array_values($categoryIds);

        $virtualProductId = $object->getId();
        $virtualProductActive = false;

        if (method_exists($object, 'getEnabled')) {
            $virtualProductActive = $object->getEnabled();
        }

        if ($object->getType() === Concrete::OBJECT_TYPE_VARIANT) {
            $parent = $object->getParent();

            while ($parent->getType() === Concrete::OBJECT_TYPE_VARIANT && $parent instanceof $object) {
                $parent = $parent->getParent();
            }

            $virtualProductId = $parent->getId();

            if (method_exists($object, 'getEnabled')) {
                $virtualProductActive = $object->getEnabled();
            }
        }

        $validLanguages = Tool::getValidLanguages();

        $data = [
            'o_id' => $object->getId(),
            'o_key' => $object->getKey(),
            'o_classId' => $object->getClassId(),
            'o_virtualProductId' => $virtualProductId,
            'o_virtualProductActive' => $virtualProductActive === null ? false : $virtualProductActive,
            'o_type' => $object->getType(),
            'categoryIds' => $convertArrayToString ? ','.implode(',', $categoryIds).',' : $categoryIds,
            'parentCategoryIds' => $convertArrayToString ? ','.implode(',', $parentCategoryIds).',' : $parentCategoryIds,
        ];

        if (method_exists($object, 'getEnabled')) {
            $data['active'] = $object->getEnabled();
        }

        if (method_exists($object, 'getShops')) {
            $data['shops'] = $convertArrayToString ? ','.@implode(',', $object->getShops()).',' : $object->getShops();
        }

        $localizedData = [
            'oo_id' => $object->getId(),
            'values' => [],
        ];

        foreach ($validLanguages as $language) {
            $localizedData['values'][$language]['name'] = $object->getName($language);
        }

        $relationData = [];
        $columnConfig = $index->getColumns();

        foreach ($columnConfig as $column) {
            if ($column instanceof IndexColumnInterface) {
                try {
                    $config = $column->getConfiguration();
                    $value = null;
                    $getter = $column->getGetter();

                    if ($column->getType() === 'localizedfields') {
                        $getter = 'get'.ucfirst($column->getObjectKey());

                        if (method_exists($object, $getter)) {
                            foreach ($validLanguages as $language) {
                                $value = $object->$getter($language);

                                $interpreterClass = $this->getInterpreterObject($column);

                                if ($interpreterClass instanceof InterpreterInterface) {
                                    $value = $interpreterClass->interpret($value, $column);

                                    if ($interpreterClass instanceof RelationInterpreterInterface) {
                                        foreach ($value as $v) {
                                            $relData = [];
                                            $relData['src'] = $object->getId();
                                            $relData['src_virtualProductId'] = $virtualProductId;
                                            $relData['dest'] = $v['dest'];
                                            $relData['fieldname'] = $column->getName();
                                            $relData['type'] = $v['type'];
                                            $relationData[] = $relData;
                                        }
                                    }
                                }

                                if (is_array($value) && $convertArrayToString) {
                                    $value = ','.implode($value, ',').',';
                                }

                                $localizedData['values'][$language][$column->getName()] = $value;
                            }
                        }
                    } else {
                        if (!empty($getter)) {
                            $getterClass = $this->getterServiceRegistry->get($getter);

                            if (Tool::classExists($getterClass)) {
                                $getterObject = new $getterClass();

                                if ($getterObject instanceof GetterInterface) {
                                    $value = $getterObject->get($object, $column);
                                } else {
                                    throw new \InvalidArgumentException(
                                        sprintf('%s needs to implement "%s", "%s" given.', $getter, GetterInterface::class, $getterClass)
                                    );
                                }
                            }
                        } else {
                            $getter = 'get'.ucfirst($column->getObjectKey());

                            if (method_exists($object, $getter)) {
                                $value = $object->$getter();
                            }
                        }

                        $interpreterClass = $this->getInterpreterObject($column);

                        if ($interpreterClass instanceof InterpreterInterface) {
                            $value = $interpreterClass->interpret($value, $column);

                            if ($interpreterClass instanceof RelationInterpreterInterface) {
                                foreach ($value as $v) {
                                    $relData = [];
                                    $relData['src'] = $object->getId();
                                    $relData['src_virtualProductId'] = $virtualProductId;
                                    $relData['dest'] = $v['dest'];
                                    $relData['fieldname'] = $column->getName();
                                    $relData['type'] = $v['type'];
                                    $relationData[] = $relData;
                                }
                            }
                        } elseif ($interpreterClass instanceof LocalizedInterpreterInterface) {
                            $validLanguages = Tool::getValidLanguages();
                            $value = null;

                            foreach ($validLanguages as $language) {
                                $localizedData['values'][$language][$column->getName()] = $interpreterClass->interpretForLanguage($language, $value, $column);
                            }
                        }

                        if ($value) {
                            if (is_array($value) && $convertArrayToString) {
                                $value = ','.implode($value, ',').',';
                            }

                            $data[$column->getName()] = $value;
                        }
                    }
                } catch (\Exception $e) {
                    Logger::err('Exception in CoreShopIndexService: '.$e->getMessage(), $e);
                }
            }
        }

        if ($a) {
            \Pimcore::setAdminMode();
        }

        AbstractObject::setGetInheritedValues($b);
        AbstractObject::setHideUnpublished($hidePublishedMemory);

        return [
            'data' => $data,
            'relation' => $relationData,
            'localizedData' => $localizedData,
        ];
    }

    /**
     * @param IndexColumnInterface $column
     *
     * @return bool|InterpreterInterface
     *
     * @throws \Exception
     */
    protected function getInterpreterObject(IndexColumnInterface $column)
    {
        $interpreter = $column->getInterpreter();

        if (!empty($interpreter)) {
            $interpreterClass = $this->interpreterServiceRegistry->get($column->getInterpreter());

            if (Tool::classExists($interpreterClass)) {
                $interpreterObject = new $interpreterClass();

                if ($interpreterObject instanceof InterpreterInterface) {
                    return $interpreterObject;
                } else {
                    throw new \InvalidArgumentException(
                        sprintf('%s needs to implement "%s", "%s" given.', $column->getInterpreter(), InterpreterInterface::class, $interpreterClass)
                    );
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function createOrUpdateIndexStructures(IndexInterface $index);

    /**
     * {@inheritdoc}
     */
    abstract public function deleteIndexStructures(IndexInterface $index);

   /**
    * {@inheritdoc}
    */
   abstract public function deleteFromIndex(IndexInterface $index, PimcoreModelInterface $object);

    /**
     * {@inheritdoc}
     */
    abstract public function updateIndex(IndexInterface $index, PimcoreModelInterface $object);

    /**
     * {@inheritdoc}
     */
    abstract public function getList(IndexInterface $index);

    /**
     * {@inheritdoc}
     */
    abstract public function renderCondition(ConditionInterface $condition);

    /**
     * {@inheritdoc}
     */
    abstract public function renderFieldType($type);

    /**
     * Get System Attributes.
     *
     * @return array
     */
    protected function getSystemAttributes()
    {
        return [
            'o_id' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'oo_id' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'name' => IndexColumnInterface::FIELD_TYPE_STRING,
            'language' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_key' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_classId' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'o_virtualProductId' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'o_virtualProductActive' => IndexColumnInterface::FIELD_TYPE_BOOLEAN,
            'o_type' => IndexColumnInterface::FIELD_TYPE_STRING,
            'categoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'parentCategoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'active' => IndexColumnInterface::FIELD_TYPE_BOOLEAN,
            'shops' => IndexColumnInterface::FIELD_TYPE_STRING,
            'minPrice' => IndexColumnInterface::FIELD_TYPE_DOUBLE,
            'maxPrice' => IndexColumnInterface::FIELD_TYPE_DOUBLE,
        ];
    }
}
