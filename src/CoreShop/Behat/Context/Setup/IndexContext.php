<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use CoreShop\Bundle\IndexBundle\Form\Type\IndexType;
use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\TableIndex;
use CoreShop\Bundle\TestBundle\Service\ClassStorageInterface;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;

final class IndexContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ClassStorageInterface $classStorage,
        private ObjectManager $objectManager,
        private FactoryInterface $indexFactory,
        private ServiceRegistryInterface $workerServiceRegistry,
        private FactoryInterface $indexColumnFactory,
        private IndexUpdaterServiceInterface $indexUpdater,
        private FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * @Given /^the site has a index "([^"]+)" for (class "[^"]+") with type "([^"]+)"$/
     * @Given /^the site has a index "([^"]+)" for (behat-class "[^"]+") with type "([^"]+)"$/
     */
    public function theSiteHasAIndexForClassWithType($name, ClassDefinition $class, $type): void
    {
        $this->createIndex($name, $class->getName(), $type);
    }

    /**
     * @Given /the (index) allows version changes$/
     */
    public function theIndexAllowsVersionChanges(IndexInterface $index): void
    {
        $index->setIndexLastVersion(true);
        $this->saveIndex($index);
    }

    /**
     * @Given /the (index) has following fields:/
     */
    public function theIndexHasFollowingFields(IndexInterface $index, TableNode $table): void
    {
        $hash = $table->getHash();

        foreach ($hash as $row) {
            /**
             * @var IndexColumnInterface $column
             */
            $column = $this->indexColumnFactory->createNew();
            $column->setName($row['name']);
            $column->setObjectKey($row['key']);
            $column->setObjectType($row['type']);
            $column->setGetter($row['getter']);
            $column->setColumnType($row['columnType']);
            $column->setDataType('input');

            if (array_key_exists('interpreter', $row)) {
                $column->setInterpreter($row['interpreter']);
            }

            if (array_key_exists('getterConfig', $row)) {
                $column->setGetterConfig(json_decode($row['getterConfig'], true));
            }

            if (array_key_exists('interpreterConfig', $row)) {
                $column->setInterpreterConfig(json_decode($row['interpreterConfig'], true));
            }

            if (array_key_exists('configuration', $row)) {
                $configuration = json_decode($row['configuration'], true);

                foreach ($configuration as $key => &$value) {
                    if ($key === 'className') {
                        $value = $this->classStorage->get($value);
                    }
                }

                $column->setConfiguration($configuration);
            }

            $index->addColumn($column);

            $this->objectManager->persist($column);
            // $row['name'], $row['email'], $row['phone']
        }

        $this->saveIndex($index);
    }

    /**
     * Submits the index form the way the Studio index editor does: columns as a plain list.
     *
     * @Given /^I save the (index) with following columns:$/
     * @When /^I save the (index) with following columns:$/
     */
    public function iSaveTheIndexWithFollowingColumns(IndexInterface $index, TableNode $table): void
    {
        $this->submitIndexForm($index, $this->getColumnsPayloadFromTable($table), true);
    }

    /**
     * @When /^I try to save the (index) with following columns:$/
     */
    public function iTryToSaveTheIndexWithFollowingColumns(IndexInterface $index, TableNode $table): void
    {
        $this->submitIndexForm($index, $this->getColumnsPayloadFromTable($table), false);
    }

    /**
     * Loads the index like the resource controller does and submits its columns unchanged.
     *
     * @When /^I save the (index) again without changes$/
     */
    public function iSaveTheIndexAgainWithoutChanges(IndexInterface $index): void
    {
        $index = $this->reloadIndex($index);

        $columnIds = [];
        $columns = [];

        foreach ($index->getColumns() as $column) {
            $columnIds[$column->getName()] = $column->getId();

            $payload = [
                'name' => $column->getName(),
                'objectKey' => $column->getObjectKey(),
                'objectType' => $column->getObjectType(),
                'dataType' => $column->getDataType(),
                'columnType' => $column->getColumnType(),
            ];

            if ($column->getInterpreter()) {
                $payload['interpreter'] = $column->getInterpreter();
            }

            $columns[] = $payload;
        }

        $this->sharedStorage->set('index_column_ids', $columnIds);

        $this->submitIndexForm($index, $columns, true);
    }

    /**
     * @Given /the (index) has an index for columns "([^"]+)"/
     */
    public function theIndexHasAnIndexForColumn(IndexInterface $index, $columns): void
    {
        $tableIndex = new TableIndex();
        $tableIndex->setType(TableIndex::TABLE_INDEX_TYPE_INDEX);
        $tableIndex->setColumns(explode(', ', $columns));

        $this->addIndexToIndex($index, $tableIndex);
    }

    /**
     * @Given /the (index) has an localized index for columns "([^"]+)"/
     */
    public function theIndexHasAnLocalizedIndexForColumn(IndexInterface $index, $columns): void
    {
        $tableIndex = new TableIndex();
        $tableIndex->setType(TableIndex::TABLE_INDEX_TYPE_INDEX);
        $tableIndex->setColumns(explode(', ', $columns));

        $this->addIndexToIndex($index, $tableIndex, true);
    }

    /**
     * @param bool           $localized
     */
    private function addIndexToIndex(IndexInterface $index, TableIndex $tableIndex, $localized = false): void
    {
        $configurationEntry = $localized ? 'localizedIndexes' : 'indexes';

        $configuration = $index->getConfiguration();

        if (!isset($configuration[$configurationEntry])) {
            $configuration[$configurationEntry] = [];
        }

        $configuration[$configurationEntry][] = $tableIndex;

        $index->setConfiguration($configuration);

        $this->saveIndex($index);
    }

    /**
     * @param string $name
     * @param string $class
     * @param string $type
     */
    private function createIndex($name, $class, $type = 'mysql'): void
    {
        /**
         * @var IndexInterface $index
         */
        $index = $this->indexFactory->createNew();
        $index->setName($name);
        $index->setClass($class);
        $index->setWorker($type);

        $this->saveIndex($index);
    }

    private function saveIndex(IndexInterface $index): void
    {
        $worker = $index->getWorker();

        if (!$this->workerServiceRegistry->has($worker)) {
            throw new \InvalidArgumentException(sprintf('%s Worker not found', $worker));
        }

        /**
         * @var WorkerInterface $worker
         */
        $worker = $this->workerServiceRegistry->get($worker);
        $worker->createOrUpdateIndexStructures($index);

        $this->objectManager->persist($index);
        $this->objectManager->flush();

        $this->sharedStorage->set('index', $index);
    }

    private function getColumnsPayloadFromTable(TableNode $table): array
    {
        $columns = [];

        foreach ($table->getHash() as $row) {
            $column = [
                'name' => $row['name'],
                'objectKey' => $row['key'],
                'objectType' => $row['type'],
                'dataType' => 'input',
                'columnType' => $row['columnType'],
            ];

            if (($row['interpreter'] ?? '') !== '') {
                $column['interpreter'] = $row['interpreter'];
            }

            $columns[] = $column;
        }

        return $columns;
    }

    private function submitIndexForm(IndexInterface $index, array $columns, bool $mustBeValid): void
    {
        $form = $this->formFactory->createNamed('', IndexType::class, $index);
        $form->submit([
            'name' => $index->getName(),
            'class' => $index->getClass(),
            'worker' => $index->getWorker(),
            'indexLastVersion' => $index->getIndexLastVersion() ? '1' : null,
            'columns' => $columns,
            'configuration' => ['indexes' => [], 'localizedIndexes' => []],
        ]);

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            if ($error instanceof FormError) {
                $errors[] = $error->getMessage();
            }
        }

        $this->sharedStorage->set('index_form_errors', $errors);

        if (!$form->isValid()) {
            if ($mustBeValid) {
                throw new \RuntimeException(sprintf('Index form is invalid: %s', implode(', ', $errors)));
            }

            return;
        }

        /**
         * @var IndexInterface $index
         */
        $index = $form->getData();

        $this->objectManager->persist($index);
        $this->objectManager->flush();

        $index = $this->reloadIndex($index);

        /**
         * @var WorkerInterface $worker
         */
        $worker = $this->workerServiceRegistry->get($index->getWorker());
        $worker->createOrUpdateIndexStructures($index);

        $this->sharedStorage->set('index', $index);
    }

    /**
     * Like the resource controller: clear the entity manager and load the index again, so its
     * columns come from the database.
     */
    private function reloadIndex(IndexInterface $index): IndexInterface
    {
        $id = $index->getId();
        $this->objectManager->clear();

        $reloaded = $this->objectManager->find($index::class, $id);

        if (!$reloaded instanceof IndexInterface) {
            throw new \RuntimeException(sprintf('Index %s not found', (string) $id));
        }

        return $reloaded;
    }

    /**
     * @Then /I generate (index "[^"]+") for products$/
     */
    public function iGenerateIndex(IndexInterface $index)
    {
        $class = $index->getClass();
        $class = ucfirst($class);
        /**
         * @psalm-var class-string $list
         */
        $list = '\Pimcore\Model\DataObject\\' . $class . '\Listing';
        /**
         * @var Listing $list
         *
         * @psalm-suppress UndefinedClass
         */
        $list = new $list();

        $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
        $list->setUnpublished(true);

        $perLoop = 10;

        $batchList = new DataObjectBatchListing($list, $perLoop);
        foreach ($batchList as $object) {
            if (!$object instanceof IndexableInterface) {
                continue;
            }
            $this->indexUpdater->updateIndices($object);
        }
    }
}
