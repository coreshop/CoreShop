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
 * Pimcore 12 / DBAL 4 dropped the doctrine "array" and "object" column types, so several CoreShop
 * columns were changed to type "json" (Configuration::data, Index/IndexColumn/FilterCondition
 * configuration, Rule Action/Condition configuration). The "array"/"object" types stored their
 * values with PHP serialize(); "json" reads them with json_decode(), so any data written by
 * CoreShop 4 is unreadable until it is converted. This migration converts those legacy
 * PHP-serialized values to JSON.
 *
 * Idempotent: rows that already contain valid JSON are skipped, so it is safe to re-run and is a
 * no-op on fresh installs. Missing tables (bundles that are not installed) are skipped.
 */
final class Version20260807000000 extends AbstractMigration
{
    /**
     * table => [identifier column, [json columns]]
     */
    private const CONVERSIONS = [
        'coreshop_configuration' => ['id', ['data']],
        'coreshop_index' => ['id', ['configuration']],
        'coreshop_index_column' => ['id', ['getterConfig', 'interpreterConfig', 'configuration']],
        'coreshop_filter_condition' => ['id', ['configuration']],
        'coreshop_rule_action' => ['id', ['configuration']],
        'coreshop_rule_condition' => ['id', ['configuration']],
    ];

    public function getDescription(): string
    {
        return 'Convert CoreShop "json" columns (Configuration, Index, Rule, ...) from legacy '
            . 'PHP-serialized (CoreShop 4 doctrine "array"/"object" types) to JSON.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SELECT 1'); // data conversion happens in postUp()
    }

    public function postUp(Schema $schema): void
    {
        foreach (self::CONVERSIONS as $table => [$idColumn, $columns]) {
            if (!$this->connection->createSchemaManager()->tablesExist([$table])) {
                $this->write(sprintf('skipping %s — table does not exist', $table));

                continue;
            }

            foreach ($columns as $column) {
                $rows = $this->connection->fetchAllAssociative(
                    "SELECT `$idColumn` AS id, `$column` AS data FROM `$table` WHERE `$column` IS NOT NULL",
                );

                $converted = 0;
                foreach ($rows as $row) {
                    json_decode((string) $row['data']);
                    if (json_last_error() === \JSON_ERROR_NONE) {
                        continue; // already valid JSON
                    }

                    $data = @unserialize($row['data'], ['allowed_classes' => false]);

                    if ($data === false && $row['data'] !== serialize(false)) {
                        $this->write(sprintf('skipping %s.%s id=%s — neither JSON nor unserializable', $table, $column, $row['id']));

                        continue;
                    }

                    $this->connection->update(
                        $table,
                        [$column => $data === null ? null : json_encode($data, JSON_THROW_ON_ERROR)],
                        [$idColumn => $row['id']],
                    );
                    ++$converted;
                }

                $this->write(sprintf('%s.%s: converted %d rows to JSON', $table, $column, $converted));
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SELECT 1');
    }

    public function postDown(Schema $schema): void
    {
        foreach (self::CONVERSIONS as $table => [$idColumn, $columns]) {
            if (!$this->connection->createSchemaManager()->tablesExist([$table])) {
                continue;
            }

            foreach ($columns as $column) {
                $rows = $this->connection->fetchAllAssociative(
                    "SELECT `$idColumn` AS id, `$column` AS data FROM `$table`
                     WHERE `$column` LIKE '{%' OR `$column` LIKE '[%'",
                );

                foreach ($rows as $row) {
                    $this->connection->update(
                        $table,
                        [$column => serialize(json_decode($row['data'], true, 512, JSON_THROW_ON_ERROR))],
                        [$idColumn => $row['id']],
                    );
                }
            }
        }
    }
}
