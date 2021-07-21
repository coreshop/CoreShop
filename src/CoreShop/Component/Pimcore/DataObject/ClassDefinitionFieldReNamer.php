<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Db\Db;
use Doctrine\DBAL\Connection;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Tool;

/**
 * @experimental Use with caution only, this is a new experimental feature
 */
class ClassDefinitionFieldReNamer implements DefinitionFieldReNamerInterface
{
    private ClassDefinition $definition;
    private string $oldFieldName;
    private string $newFieldName;
    private Connection $database;

    public function __construct(ClassDefinition $definition, string $oldFieldName, string $newFieldName)
    {
        $this->definition = $definition;
        $this->newFieldName = $newFieldName;
        $this->oldFieldName = $oldFieldName;
        $this->database = Db::getDoctrineConnection();
    }

    public function rename(): void
    {
        $queries = $this->getRenameQueries();

        $this->database->transactional(
            function () use ($queries) {
                foreach ($queries as $qry) {
                    $this->database->executeQuery($qry);
                }
            }
        );
    }

    public function getDefinition(): ClassDefinition
    {
        return $this->definition;
    }

    public function getOldFieldName(): string
    {
        return $this->oldFieldName;
    }

    public function getNewFieldName(): string
    {
        return $this->newFieldName;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    protected function getRenameQueries(): array
    {
        $fieldDefinition = $this->definition->getFieldDefinition($this->oldFieldName);
        $isLocalizedField = false;

        if (!$fieldDefinition) {
            $localizedFields = $this->definition->getFieldDefinition('localizedfields');

            if ($localizedFields instanceof Data\Localizedfields) {
                $fieldDefinition = $localizedFields->getFielddefinition($this->oldFieldName);
                $isLocalizedField = true;
            }
        }

        if (!$fieldDefinition) {
            throw new \InvalidArgumentException(sprintf('Field definition %s not found', $this->oldFieldName));
        }

        $schemaManager = $this->database->getSchemaManager();

        $storeTables = ['object_store_' . $this->definition->getId()];
        $queryTables = ['object_query_' . $this->definition->getId()];
        $tableRelations = 'object_relations_' . $this->definition->getId();

        //Check for localized data
        $storeTables[] = 'object_localized_data_' . $this->definition->getId();

        $validLanguages = Tool::getValidLanguages();

        foreach ($validLanguages as $language) {
            $queryTables[] = 'object_localized_query_' . $this->definition->getId() . '_' . $language;
        }

        $key = $fieldDefinition->getName();

        $columnRenames = [];

        foreach ($queryTables as $queryTable) {
            if ($fieldDefinition instanceof Data\QueryResourcePersistenceAwareInterface) {
                if (is_array($fieldDefinition->getQueryColumnType())) {
                    foreach ($fieldDefinition->getQueryColumnType() as $fkey => $fvalue) {
                        $columnName = $key.'__'.$fkey;
                        $newColumnName = $key.'__'.$this->newFieldName;

                        $columnRenames[$queryTable][$columnName] = $newColumnName;
                    }
                }

                if (!is_array($fieldDefinition->getQueryColumnType())) {
                    if ($fieldDefinition->getQueryColumnType()) {
                        $columnRenames[$queryTable][$key] = $this->newFieldName;
                    }
                }
            }
        }

        foreach ($storeTables as $storeTable) {
            if ($fieldDefinition instanceof Data\ResourcePersistenceAwareInterface) {
                if ($fieldDefinition instanceof Data && !$fieldDefinition->isRelationType() && is_array($fieldDefinition->getColumnType())) {
                    foreach ($fieldDefinition->getColumnType() as $fkey => $fvalue) {
                        $columnName = $key.'__'.$fkey;
                        $newColumnName = $key.'__'.$this->newFieldName;

                        $columnRenames[$storeTable][$columnName] = $newColumnName;
                    }
                }

                if (!is_array($fieldDefinition->getColumnType())) {
                    if ($fieldDefinition instanceof Data && $fieldDefinition->getColumnType() && !$fieldDefinition->isRelationType()) {
                        $columnRenames[$storeTable][$key] = $this->newFieldName;
                    }
                }
            }
        }

        $queries = [];

        foreach ($columnRenames as $tableName => $fields) {
            $schema = $schemaManager->listTableDetails($tableName);

            foreach ($fields as $from => $to) {
                if ($schema->hasColumn($from)) {
                    $column = $schema->getColumn($from);
                    $currentType = $column->getType()->getSQLDeclaration(
                        $column->toArray(),
                        $this->database->getDatabasePlatform()
                    );

                    $queries[] = sprintf('ALTER TABLE `%s` CHANGE `%s` `%s` %s', $tableName, $from, $to, $currentType);
                }
            }
        }

        if ($fieldDefinition instanceof Data\Objectbricks) {
            $brickDefinitions = new Objectbrick\Definition\Listing();
            $brickDefinitions = $brickDefinitions->load();

            /**
             * @var Objectbrick\Definition $brickDefinition
             */
            foreach ($brickDefinitions as $brickDefinition) {
                $brickQueryTable = $brickDefinition->getDao()->getTableName($this->definition, true);
                $brickStoreTable = $brickDefinition->getDao()->getTableName($this->definition, false);

                if ($schemaManager->tablesExist([$brickQueryTable, $brickStoreTable])) {
                    $newBrickQueryTable = 'object_brick_query_' . $this->newFieldName . '_' . $this->definition->getId();
                    $newBrickStoreTable = 'object_brick_store_' . $this->newFieldName . '_' . $this->definition->getId();

                    $queries[] = sprintf('RENAME TABLE `%s` TO `%s`;', $brickQueryTable, $newBrickQueryTable);
                    $queries[] = sprintf('RENAME TABLE `%s` TO `%s`;', $brickStoreTable, $newBrickStoreTable);
                }
            }
        }

        if ($fieldDefinition instanceof Data\Fieldcollections) {
            foreach ($fieldDefinition->getAllowedTypes() as $fieldCollectionType) {
                $collectionDefinition = Fieldcollection\Definition::getByKey($fieldCollectionType);

                $collectionTable = $collectionDefinition->getDao()->getTableName($this->definition);
                $collectionLocalizedTable = $collectionDefinition->getDao()->getLocalizedTableName($this->definition);

                if ($schemaManager->tablesExist([$collectionTable, $collectionLocalizedTable])) {
                    $newCollectionTable = 'object_collection_' . $this->newFieldName . '_' . $this->definition->getId();
                    $newCollectionLocalizedTable = 'object_collection_' . $this->newFieldName . '_localized_' . $this->definition->getId(
                    );

                    $queries[] = sprintf('RENAME TABLE `%s` TO `%s`;', $collectionTable, $newCollectionTable);
                    $queries[] = sprintf(
                        'RENAME TABLE `%s` TO `%s`;',
                        $collectionLocalizedTable,
                        $newCollectionLocalizedTable
                    );
                }
            }
        }

        if ($fieldDefinition->isRelationType()) {
            $queries[] = sprintf(
                'UPDATE %s SET `fieldname`=\'%s\' WHERE `fieldname`=\'%s\'',
                $tableRelations,
                $this->oldFieldName,
                $this->newFieldName
            );
        }

        $fieldDefinition->setName($this->newFieldName);

        if ($isLocalizedField) {
            /**
             * @var Data\Localizedfields $localizedFieldDefinition
             */
            $localizedFieldDefinition = $this->definition->getFieldDefinition('localizedfields');
            $localizedFieldDefinition->fieldDefinitionsCache = [];
        } else {
            $fieldDefinitions = $this->definition->getFieldDefinitions();

            unset($fieldDefinitions[$this->oldFieldName]);

            $this->definition->setFieldDefinitions($fieldDefinitions);
            $this->definition->addFieldDefinition($this->newFieldName, $fieldDefinition);
        }

        return $queries;
    }
}
