<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Getter\GetterInterface;
use CoreShop\Component\Index\Interpreter\InterpreterInterface;
use CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface;
use CoreShop\Component\Index\Interpreter\RelationalValueInterface;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Order\OrderInterface;
use CoreShop\Component\Index\Order\OrderRendererInterface;
use CoreShop\Component\Index\Worker\FilterGroupHelperInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Tool;
use Psr\Log\LoggerAwareTrait;
use Webmozart\Assert\Assert;

abstract class AbstractWorker implements WorkerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected ServiceRegistryInterface $extensions,
        protected ServiceRegistryInterface $getterServiceRegistry,
        protected ServiceRegistryInterface $interpreterServiceRegistry,
        protected FilterGroupHelperInterface $filterGroupHelper,
        protected ConditionRendererInterface $conditionRenderer,
        protected OrderRendererInterface $orderRenderer,
    ) {
    }

    public function getExtensions(IndexInterface $index): array
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

    public function renderCondition(ConditionInterface $condition, $prefix = null)
    {
        return $this->conditionRenderer->render($this, $condition, $prefix);
    }

    public function renderOrder(OrderInterface $condition, $prefix = null)
    {
        return $this->orderRenderer->render($this, $condition, $prefix);
    }

    protected function prepareData(IndexInterface $index, IndexableInterface $object): array
    {
        $inAdmin = \Pimcore::inAdmin();
        \Pimcore::unsetAdminMode();
        $hidePublishedMemory = AbstractObject::doHideUnpublished();
        AbstractObject::setHideUnpublished(false);

        $extensions = $this->getExtensions($index);

        $virtualObjectId = $object->getId();
        $virtualObjectActive = $object->getIndexableEnabled($index);

        if ($object->getType() === Concrete::OBJECT_TYPE_VARIANT) {
            /**
             * @var Concrete $parent
             */
            $parent = $object->getParent();

            while ($parent->getType() === Concrete::OBJECT_TYPE_VARIANT && $parent instanceof $object) {
                $parent = $parent->getParent();
            }

            $virtualObjectId = $parent->getId();
            $virtualObjectActive = $parent instanceof IndexableInterface ? $parent->getIndexableEnabled($index) : false;
        }

        $data = [
            'o_id' => $object->getId(),
            'o_key' => $object->getKey(),
            'o_classId' => $object->getClassId(),
            'o_className' => $object->getClassName(),
            'o_virtualObjectId' => $virtualObjectId,
            'o_virtualObjectActive' => $virtualObjectActive,
            'o_type' => $object->getType(),
        ];

        foreach ($extensions as $extension) {
            if ($extension instanceof IndexColumnsExtensionInterface) {
                $data = array_merge($data, $extension->getIndexColumns($object));
            }
        }

        $data['active'] = $object->getIndexableEnabled($index);

        $localizedData = [
            'oo_id' => $object->getId(),
            'values' => [],
        ];

        foreach (Tool::getValidLanguages() as $language) {
            $localizedData['values'][$language]['name'] = $object->getIndexableName($index, $language);
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
                    $getter = 'get' . ucfirst($column->getObjectKey());
                    if (method_exists($object, $getter)) {
                        $value = $object->$getter();
                    }
                }

                [$columnLocalizedData, $columnRelationData, $value, $isLocalizedValue] = $this->processInterpreter($column, $object, $value, $virtualObjectId);

                $relationData = array_merge_recursive($relationData, $columnRelationData);
                $localizedData = array_merge_recursive($localizedData, $columnLocalizedData);

                if (!$isLocalizedValue) {
                    if (is_array($value)) {
                        $value = $this->handleArrayValues($index, $value);
                    }

                    $value = $this->typeCastValues($column, $value);

                    $data[$column->getName()] = $value;
                }
            } catch (\Exception $e) {
                $this->logger->error('Exception in CoreShopIndexService: ' . $e->getMessage(), [$e]);

                throw $e;
            }
        }

        $result = [
            'data' => $data,
            'relation' => $relationData,
            'localizedData' => $localizedData,
        ];

        if ($inAdmin) {
            \Pimcore::setAdminMode();
        }

        AbstractObject::setHideUnpublished($hidePublishedMemory);

        return $result;
    }

    abstract protected function typeCastValues(IndexColumnInterface $column, $value);

    abstract protected function handleArrayValues(IndexInterface $index, array $value);

    protected function processRelationalData(IndexColumnInterface $column, IndexableInterface $object, mixed $value, int $virtualObjectId): array
    {
        if (null === $value) {
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

                foreach ($v->getParams() as $key => $val) {
                    $relData[$key] = $val;
                }
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

    protected function processGetter(IndexColumnInterface $column, IndexableInterface $object)
    {
        $getter = $column->getGetter();
        $getterClass = $this->getterServiceRegistry->get($getter);

        Assert::isInstanceOf($getterClass, GetterInterface::class);

        return $getterClass->get($object, $column);
    }

    protected function processInterpreter(IndexColumnInterface $column, IndexableInterface $object, $originalValue, int $virtualObjectId): array
    {
        $value = $originalValue;
        $relationData = [];
        $localizedData = [];
        $isLocalizedValue = false;

        $interpreterClass = $this->getInterpreterObject($column);

        if ($interpreterClass instanceof LocalizedInterpreterInterface) {
            foreach (Tool::getValidLanguages() as $language) {
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

    protected function getInterpreterObject(IndexColumnInterface $column): ?InterpreterInterface
    {
        $interpreter = $column->getInterpreter();

        if (!empty($interpreter)) {
            $interpreterObject = $this->interpreterServiceRegistry->get($column->getInterpreter());

            if ($interpreterObject instanceof InterpreterInterface) {
                return $interpreterObject;
            }

            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', $column->getInterpreter(), InterpreterInterface::class, $interpreter),
            );
        }

        return null;
    }

    public function getFilterGroupHelper(): FilterGroupHelperInterface
    {
        return $this->filterGroupHelper;
    }

    abstract public function createOrUpdateIndexStructures(IndexInterface $index);

    abstract public function deleteIndexStructures(IndexInterface $index);

    abstract public function deleteFromIndex(IndexInterface $index, IndexableInterface $object);

    abstract public function updateIndex(IndexInterface $index, IndexableInterface $object);

    abstract public function getList(IndexInterface $index): ListingInterface;

    abstract public function renderFieldType(string $type);

    protected function getSystemAttributes(): array
    {
        return [
            'o_id' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'oo_id' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'o_key' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_classId' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_className' => IndexColumnInterface::FIELD_TYPE_STRING,
            'o_virtualObjectId' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'o_virtualObjectActive' => IndexColumnInterface::FIELD_TYPE_BOOLEAN,
            'o_type' => IndexColumnInterface::FIELD_TYPE_STRING,
            'active' => IndexColumnInterface::FIELD_TYPE_BOOLEAN,
        ];
    }

    protected function getLocalizedSystemAttributes(): array
    {
        return [
            'o_id' => IndexColumnInterface::FIELD_TYPE_INTEGER,
            'language' => IndexColumnInterface::FIELD_TYPE_STRING,
            'name' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }
}
