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
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Getter\GetterInterface;
use CoreShop\Component\Index\Interpreter\InterpreterInterface;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Interpreter\RelationalValueInterface;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\FilterGroupHelperInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;
use Psr\Log\LoggerAwareTrait;
use Webmozart\Assert\Assert;

abstract class AbstractWorker implements WorkerInterface
{
    use LoggerAwareTrait;

    /**
     * @var ServiceRegistryInterface
     */
    protected $extensions;

    /**
     * @var ServiceRegistryInterface
     */
    protected $getterServiceRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $interpreterServiceRegistry;

    /**
     * @var FilterGroupHelperInterface
     */
    protected $filterGroupHelper;

    /**
     * @param ServiceRegistryInterface   $extensions
     * @param ServiceRegistryInterface   $getterServiceRegistry
     * @param ServiceRegistryInterface   $interpreterServiceRegistry
     * @param FilterGroupHelperInterface $filterGroupHelper
     */
    public function __construct(
        ServiceRegistryInterface $extensions,
        ServiceRegistryInterface $getterServiceRegistry,
        ServiceRegistryInterface $interpreterServiceRegistry,
        FilterGroupHelperInterface $filterGroupHelper
    ) {
        $this->extensions = $extensions;
        $this->getterServiceRegistry = $getterServiceRegistry;
        $this->interpreterServiceRegistry = $interpreterServiceRegistry;
        $this->filterGroupHelper = $filterGroupHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions(IndexInterface $index)
    {
        $extensions = $this->extensions->all();
        $eligibleExtensions = [];

        foreach ($extensions as $extension) {
            if ($extension instanceof IndexExtensionInterface && $extension->supports($index)) {
                $eligibleExtensions[] = $extension;
            }
        }

        return $eligibleExtensions;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareData(IndexInterface $index, IndexableInterface $object)
    {
        $inAdmin = \Pimcore::inAdmin();
        \Pimcore::unsetAdminMode();
        $hidePublishedMemory = AbstractObject::doHideUnpublished();
        AbstractObject::setHideUnpublished(false);

        $result = InheritanceHelper::useInheritedValues(function () use ($index, $object) {
            $extensions = $this->getExtensions($index);

            $virtualObjectId = $object->getId();
            $virtualObjectActive = $object->getEnabled();

            if (Concrete::OBJECT_TYPE_VARIANT === $object->getType()) {
                $parent = $object->getParent();

                while (Concrete::OBJECT_TYPE_VARIANT === $parent->getType() && $parent instanceof $object) {
                    $parent = $parent->getParent();
                }

                $virtualObjectId = $parent->getId();
                $virtualObjectActive = $object->getEnabled();
            }

            $validLanguages = Tool::getValidLanguages();

            $data = [
                'o_id' => $object->getId(),
                'o_key' => $object->getKey(),
                'o_classId' => $object->getClassId(),
                'o_className' => $object->getClassName(),
                'o_virtualObjectId' => $virtualObjectId,
                'o_virtualObjectActive' => null === $virtualObjectActive ? false : $virtualObjectActive,
                'o_type' => $object->getType(),
            ];

            foreach ($extensions as $extension) {
                if ($extension instanceof IndexColumnsExtensionInterface) {
                    $data = array_merge($data, $extension->getIndexColumns($object));
                }
            }

            $data['active'] = $object->getEnabled();

            if (!is_bool($data['active'])) {
                $data['active'] = false;
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
                Assert::isInstanceOf($column, IndexColumnInterface::class);

                try {
                    $value = null;
                    if ($column->hasGetter()) {
                        $value = $this->processGetter($column, $object);
                    } else {
                        $getter = 'get'.ucfirst($column->getObjectKey());

                        if (method_exists($object, $getter)) {
                            $value = $object->$getter();
                        }
                    }

                    list($columnLocalizedData, $columnRelationData, $value, $isLocalizedValue) = $this->processInterpreter($column, $object, $value, $virtualObjectId);

                    $relationData = array_merge_recursive($relationData, $columnRelationData);
                    $localizedData = array_merge_recursive($localizedData, $columnLocalizedData);

                    if (!$isLocalizedValue) {
                        if (is_array($value)) {
                            $value = ','.implode($value, ',').',';
                        }

                        $value = $this->typeCastValues($column, $value);

                        $data[$column->getName()] = $value;
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Exception in CoreShopIndexService: '.$e->getMessage(), [$e]);

                    throw $e;
                }
            }

            return [
                'data' => $data,
                'relation' => $relationData,
                'localizedData' => $localizedData,
            ];
        });

        if ($inAdmin) {
            \Pimcore::setAdminMode();
        }

        AbstractObject::setHideUnpublished($hidePublishedMemory);

        return $result;
    }

    /**
     * @param IndexColumnInterface $column
     * @param $value
     *
     * @return mixed
     */
    protected function typeCastValues(IndexColumnInterface $column, $value)
    {
        switch ($column->getColumnType()) {
            case IndexColumnInterface::FIELD_TYPE_INTEGER:
                return intval($value);

            case IndexColumnInterface::FIELD_TYPE_BOOLEAN:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case IndexColumnInterface::FIELD_TYPE_DATE:
                if ($value instanceof \DateTime) {
                    return $value->format('Y-m-d H:i:s');
                }

                return null;

            case IndexColumnInterface::FIELD_TYPE_DOUBLE:
                return doubleval($value);

            case IndexColumnInterface::FIELD_TYPE_STRING:
                return strval($value);

            case IndexColumnInterface::FIELD_TYPE_TEXT:
                return strval($value);
        }

        throw new \InvalidArgumentException(sprintf(
            'Unknown type %s given, valid types are %s',
            $column->getColumnType(),
            implode(', ', [
                IndexColumnInterface::FIELD_TYPE_STRING,
                IndexColumnInterface::FIELD_TYPE_DOUBLE,
                IndexColumnInterface::FIELD_TYPE_INTEGER,
                IndexColumnInterface::FIELD_TYPE_BOOLEAN,
                IndexColumnInterface::FIELD_TYPE_DATE,
                IndexColumnInterface::FIELD_TYPE_TEXT,
            ])
        ));
    }

    /**
     * @param IndexColumnInterface $column
     * @param IndexableInterface   $object
     * @param $value
     * @param $virtualObjectId
     *
     * @return array
     */
    protected function processRelationalData(IndexColumnInterface $column, IndexableInterface $object, $value, $virtualObjectId)
    {
        if (is_null($value)) {
            return [];
        }

        $relationData = [];

        foreach ($value as $v) {
            $relData = [];
            $relData['src'] = $object->getId();
            $relData['src_virtualObjectId'] = $virtualObjectId;
            $relData['fieldname'] = $column->getName();

            if ($v instanceof RelationalValueInterface) {
                $relData['dest'] = $v->getDestinationId();
                $relData['type'] = $v->getType();
            } elseif (is_array($v) && array_key_exists('dest', $v) && array_key_exists('type', $v)) {
                $relData['dest'] = $v['dest'];
                $relData['type'] = $v['type'];
            } else {
                throw new \InvalidArgumentException(sprintf('Result needs either be instanceof %s or an array with `id` and `type`', RelationalValueInterface::class));
            }

            $relationData[] = $relData;
        }

        return $relationData;
    }

    /**
     * @param IndexColumnInterface $column
     * @param IndexableInterface   $object
     *
     * @return mixed|null
     */
    protected function processGetter(IndexColumnInterface $column, IndexableInterface $object)
    {
        $getter = $column->getGetter();
        $getterClass = $this->getterServiceRegistry->get($getter);
        $value = null;

        Assert::isInstanceOf($getterClass, GetterInterface::class);

        return $getterClass->get($object, $column);
    }

    /**
     * @param IndexColumnInterface $column
     * @param IndexableInterface   $object
     * @param $originalValue
     * @param $virtualObjectId
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function processInterpreter(IndexColumnInterface $column, IndexableInterface $object, $originalValue, $virtualObjectId)
    {
        $value = $originalValue;
        $relationData = [];
        $localizedData = [];
        $isLocalizedValue = false;

        $interpreterClass = $this->getInterpreterObject($column);

        if ($interpreterClass instanceof LocalizedInterpreterInterface) {
            $validLanguages = Tool::getValidLanguages();
            foreach ($validLanguages as $language) {
                $localizedData['values'][$language][$column->getName()] = $interpreterClass->interpretForLanguage($language, $value, $object, $column, $column->getInterpreterConfig());
            }
            //reset value here, we only populate localized values here
            $value = null;
            $isLocalizedValue = true;
        } elseif ($interpreterClass instanceof InterpreterInterface) {
            $value = $interpreterClass->interpret($originalValue, $object, $column, $column->getInterpreterConfig());

            if ($interpreterClass instanceof RelationInterpreterInterface) {
                $relationalValue = $interpreterClass->interpretRelational($originalValue, $object, $column, $column->getInterpreterConfig());

                $relationData = array_merge_recursive($relationData, $this->processRelationalData($column, $object, $relationalValue, $virtualObjectId));
            }
        }

        return [
            $localizedData, $relationData, $value, $isLocalizedValue,
        ];
    }

    /**
     * @param IndexColumnInterface $column
     *
     * @return InterpreterInterface|bool
     *
     * @throws \Exception
     */
    protected function getInterpreterObject(IndexColumnInterface $column)
    {
        $interpreter = $column->getInterpreter();

        if (!empty($interpreter)) {
            $interpreterObject = $this->interpreterServiceRegistry->get($column->getInterpreter());

            if ($interpreterObject instanceof InterpreterInterface) {
                return $interpreterObject;
            } else {
                throw new \InvalidArgumentException(
                    sprintf('%s needs to implement "%s", "%s" given.', $column->getInterpreter(), InterpreterInterface::class, $interpreter)
                );
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterGroupHelper()
    {
        return $this->filterGroupHelper;
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
    abstract public function deleteFromIndex(IndexInterface $index, IndexableInterface $object);

    /**
     * {@inheritdoc}
     */
    abstract public function updateIndex(IndexInterface $index, IndexableInterface $object);

    /**
     * {@inheritdoc}
     */
    abstract public function getList(IndexInterface $index);

    /**
     * {@inheritdoc}
     */
    abstract public function renderCondition(ConditionInterface $condition, $prefix = null);

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
            'o_key' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_classId' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'o_className' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_virtualObjectId' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'o_virtualObjectActive' => IndexColumnInterface::FIELD_TYPE_BOOLEAN,
            'o_type' => IndexColumnInterface::FIELD_TYPE_STRING,
            'active' => IndexColumnInterface::FIELD_TYPE_BOOLEAN,
        ];
    }

    /**
     * Get System Attributes.
     *
     * @return array
     */
    protected function getLocalizedSystemAttributes()
    {
        return [
            'o_id' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'language' => IndexColumnInterface::FIELD_TYPE_STRING,
            'name' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }
}
