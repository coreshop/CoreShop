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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use CoreShop\Behat\Service\ClassStorageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\TableIndex;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\DataObject\ClassDefinition;

final class IndexContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ClassStorageInterface $classStorage;
    private ObjectManager $objectManager;
    private FactoryInterface $indexFactory;
    private ServiceRegistryInterface $workerServiceRegistry;
    private FactoryInterface $indexColumnFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ClassStorageInterface $classStorage,
        ObjectManager $objectManager,
        FactoryInterface $indexFactory,
        ServiceRegistryInterface $workerServiceRegistry,
        FactoryInterface $indexColumnFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->classStorage = $classStorage;
        $this->objectManager = $objectManager;
        $this->indexFactory = $indexFactory;
        $this->workerServiceRegistry = $workerServiceRegistry;
        $this->indexColumnFactory = $indexColumnFactory;
    }

    /**
     * @Given /^the site has a index "([^"]+)" for (class "[^"]+") with type "([^"]+)"$/
     * @Given /^the site has a index "([^"]+)" for (behat-class "[^"]+") with type "([^"]+)"$/
     */
    public function theSiteHasAIndexForClassWithType($name, ClassDefinition $class, $type)
    {
        $this->createIndex($name, $class->getName(), $type);
    }

    /**
     * @Given /the (index) allows version changes$/
     */
    public function theIndexAllowsVersionChanges(IndexInterface $index)
    {
        $index->setIndexLastVersion(true);
        $this->saveIndex($index);
    }

    /**
     * @Given /the (index) has following fields:/
     */
    public function theIndexHasFollowingFields(IndexInterface $index, TableNode $table)
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
     * @Given /the (index) has an index for columns "([^"]+)"/
     */
    public function theIndexHasAnIndexForColumn(IndexInterface $index, $columns)
    {
        $tableIndex = new TableIndex();
        $tableIndex->setType(TableIndex::TABLE_INDEX_TYPE_INDEX);
        $tableIndex->setColumns(explode(', ', $columns));

        $this->addIndexToIndex($index, $tableIndex);
    }

    /**
     * @Given /the (index) has an localized index for columns "([^"]+)"/
     */
    public function theIndexHasAnLocalizedIndexForColumn(IndexInterface $index, $columns)
    {
        $tableIndex = new TableIndex();
        $tableIndex->setType(TableIndex::TABLE_INDEX_TYPE_INDEX);
        $tableIndex->setColumns(explode(', ', $columns));

        $this->addIndexToIndex($index, $tableIndex, true);
    }

    /**
     * @param IndexInterface $index
     * @param TableIndex     $tableIndex
     * @param bool           $localized
     */
    private function addIndexToIndex(IndexInterface $index, TableIndex $tableIndex, $localized = false)
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
    private function createIndex($name, $class, $type = 'mysql')
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

    /**
     * @param IndexInterface $index
     */
    private function saveIndex(IndexInterface $index)
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
}
