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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Service;

use CoreShop\Bundle\CoreBundle\Installer;
use Doctrine\DBAL\DriverManager;

class Setup
{
    protected static bool $pimcoreSetupDone = false;

    protected static bool $coreShopSetupDone = false;

    public static function setupPimcore(): void
    {
        if (getenv('CORESHOP_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$pimcoreSetupDone) {
            return;
        }

        $connection = \Pimcore::getContainer()->get('database_connection');

        if (null === $connection) {
            throw new \Exception('Database connection not found');
        }

        $dbName = $connection->getParams()['dbname'];
        $params = $connection->getParams();
        $config = $connection->getConfiguration();

        unset($params['url'], $params['dbname']);

        // use a dedicated setup connection as the framework connection is bound to the DB and will
        // fail if the DB doesn't exist
        $setupConnection = DriverManager::getConnection($params, $config);
        $schemaManager = $setupConnection->getSchemaManager();

        $databases = $schemaManager->listDatabases();
        if (in_array($dbName, $databases)) {
            $schemaManager->dropDatabase($connection->quoteIdentifier($dbName));
        }

        $schemaManager->createDatabase($connection->quoteIdentifier($dbName));

        if (!$connection->isConnected()) {
            $connection->connect();
        }

        $installer = new \Pimcore\Bundle\InstallBundle\Installer(
            \Pimcore::getContainer()->get('monolog.logger.pimcore'),
            \Pimcore::getContainer()->get('event_dispatcher'),
        );

        $method = new \ReflectionMethod($installer, 'setupDatabase');
        $num = $method->getNumberOfParameters();

        if ($num >= 3) {
            $installer->setupDatabase($connection, [
                'username' => 'admin',
                'password' => 'coreshop',
            ]);
        } else {
            /**
             * @phpstan-ignore-next-line
             */
            $installer->setupDatabase([
                'username' => 'admin',
                'password' => 'coreshop',
            ]);
        }
        static::$pimcoreSetupDone = true;
    }

    public static function setupDone(): bool
    {
        return getenv('CORESHOP_SKIP_DB_SETUP') || (static::$pimcoreSetupDone && static::$coreShopSetupDone);
    }

    public static function setupCoreShop(): void
    {
        if (getenv('CORESHOP_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$coreShopSetupDone) {
            return;
        }

        $installer = \Pimcore::getContainer()->get(Installer::class);
        $installer->install();

        static::$coreShopSetupDone = true;
    }
}
