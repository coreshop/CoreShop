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

namespace CoreShop\Bundle\ResourceBundle\Command;

use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;

abstract class AbstractDatabaseTablesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    /**
     * @phpstan-param list<ClassMetadata> $metadatas
     */
    protected function createDiffSchemaSqls(array $metadatas, bool $down): array
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $coreShopSchema = $schemaTool->getSchemaFromMetadata($metadatas);
        $tableNames = array_map(
            static function (Table $table) {
                /** @psalm-suppress InternalMethod */
                return $table->getName();
            },
            $coreShopSchema->getTables(),
        );

        $configuration = $this->entityManager->getConnection()->getConfiguration();
        $assetFilter = $configuration->getSchemaAssetsFilter();
        $configuration->setSchemaAssetsFilter(function (mixed $tableName) use ($tableNames) {
            if ($tableName instanceof AbstractAsset) {
                /** @psalm-suppress InternalMethod */
                $tableName = $tableName->getName();
            }

            if (!\is_string($tableName)) {
                throw new \TypeError(
                    sprintf(
                        'The table name must be an instance of "%s" or a string ("%s" given).',
                        AbstractAsset::class,
                        get_debug_type($tableName),
                    ),
                );
            }

            return in_array($tableName, $tableNames, true);
        });

        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        $comparator = $schemaManager->createComparator();

        if ($down) {
            $schemaDiff = $comparator->compareSchemas($schemaManager->introspectSchema(), new Schema());
        } else {
            $schemaDiff = $comparator->compareSchemas($schemaManager->introspectSchema(), $coreShopSchema);
        }

        $sqls = $this->entityManager->getConnection()->getDatabasePlatform()->getAlterSchemaSQL($schemaDiff);

        $configuration->setSchemaAssetsFilter($assetFilter);

        return $sqls;
    }
}
