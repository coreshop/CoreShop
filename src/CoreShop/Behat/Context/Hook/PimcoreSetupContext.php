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

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Tool\Setup;

final class PimcoreSetupContext implements Context
{
    /**
     * @BeforeSuite
     */
    public static function setupPimcore()
    {
        return;
        $connection = \Pimcore::getContainer()->get('database_connection');
        
        $dbName = $connection->getParams()['dbname'];
        $params = $connection->getParams();
        $config = $connection->getConfiguration();

        unset($params['url']);
        unset($params['dbname']);

        // use a dedicated setup connection as the framework connection is bound to the DB and will
        // fail if the DB doesn't exist
        $setupConnection = DriverManager::getConnection($params, $config);
        $schemaManager   = $setupConnection->getSchemaManager();

        $databases = $schemaManager->listDatabases();
        if (in_array($dbName, $databases)) {
            $schemaManager->dropDatabase($connection->quoteIdentifier($dbName));
        }

        $schemaManager->createDatabase($connection->quoteIdentifier($dbName));


        if (!$connection->isConnected()) {
            $connection->connect();
        }

        $setup = new Setup();
        $setup->database();

        $setup->contents([
            'username' => 'admin',
            'password' => microtime()
        ]);
    }
}
