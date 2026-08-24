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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Companion of Version20260807000000: that migration converted the values of the columns that were
 * mapped as doctrine "array"/"object" before Pimcore 12 / DBAL 4 to JSON, but it did not touch the
 * column definitions. On databases created before the mapping change those columns are still
 * LONGTEXT while the mapping now says "json", which keeps showing up as a schema diff.
 *
 * Kept separate from the fieldName migration (Version20260821090000) because it is unrelated drift:
 * it is cosmetic (both column types read and write the same values through DBAL), it must run after
 * the value conversion of Version20260807000000, and it is skipped per column whenever the data
 * cannot be converted, which should not hold back the fix for a broken installation.
 *
 * Idempotent: columns that already have the platform's json type are skipped, and a column whose
 * values are not all valid JSON is left alone with a warning instead of failing the migration
 * (MySQL rejects invalid JSON on ALTER TABLE). On platforms whose json declaration is LONGTEXT
 * (MariaDB) the whole migration is a no-op.
 */
final class Version20260821090100 extends AbstractMigration
{
    /**
     * table => columns
     *
     * @var array<string, list<string>>
     */
    private const COLUMNS = [
        'coreshop_configuration' => ['data'],
        'coreshop_index' => ['configuration'],
        'coreshop_index_column' => ['getterConfig', 'interpreterConfig', 'configuration'],
        'coreshop_filter_condition' => ['configuration'],
        'coreshop_rule_action' => ['configuration'],
        'coreshop_rule_condition' => ['configuration'],
    ];

    public function getDescription(): string
    {
        return 'Change the columns mapped as doctrine "json" (Configuration, Index, Rule, ...) from '
            . 'the legacy LONGTEXT definition to the platform json type.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SELECT 1'); // the conversion happens in postUp()
    }

    public function postUp(Schema $schema): void
    {
        $jsonDeclaration = $this->platform->getJsonTypeDeclarationSQL([]);

        if (str_contains(strtoupper($jsonDeclaration), 'TEXT')) {
            $this->write(sprintf('nothing to do, this platform declares json columns as %s', $jsonDeclaration));

            return;
        }

        $schemaManager = $this->connection->createSchemaManager();

        foreach (self::COLUMNS as $table => $columns) {
            if (!$schemaManager->tablesExist([$table])) {
                $this->write(sprintf('skipping %s — table does not exist', $table));

                continue;
            }

            $tableColumns = $schemaManager->listTableColumns($table);

            foreach ($columns as $column) {
                $tableColumn = $tableColumns[strtolower($column)] ?? null;

                if (null === $tableColumn) {
                    $this->write(sprintf('skipping %s.%s — column does not exist', $table, $column));

                    continue;
                }

                if (!$this->isConvertible($table, $column)) {
                    continue;
                }

                $this->connection->executeStatement(sprintf(
                    'ALTER TABLE `%s` MODIFY `%s` %s %s',
                    $table,
                    $column,
                    $jsonDeclaration,
                    $tableColumn->getNotnull() ? 'NOT NULL' : 'DEFAULT NULL',
                ));

                $this->write(sprintf('%s.%s converted to %s', $table, $column, $jsonDeclaration));
            }
        }
    }

    public function down(Schema $schema): void
    {
        // The mapping expects json columns; there is nothing to roll back to.
        $this->addSql('SELECT 1');
    }

    private function isConvertible(string $table, string $column): bool
    {
        $currentType = $this->connection->fetchOne(
            'SELECT DATA_TYPE FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column',
            ['table' => $table, 'column' => $column],
        );

        if (is_string($currentType) && strtolower($currentType) === 'json') {
            return false;
        }

        $values = $this->connection->fetchFirstColumn(
            sprintf('SELECT `%s` FROM `%s` WHERE `%s` IS NOT NULL', $column, $table, $column),
        );

        foreach ($values as $value) {
            json_decode((string) $value);

            if (json_last_error() !== \JSON_ERROR_NONE) {
                $this->write(sprintf(
                    'skipping %s.%s — it still contains values that are not valid JSON, run the value '
                    . 'conversion (Version20260807000000) first',
                    $table,
                    $column,
                ));

                return false;
            }
        }

        return true;
    }
}
