<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\Setup;
use CoreShop\Bundle\CoreBundle\Test\Service\NotificationRuleListenerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CoreShopSetupContext implements Context
{
    public function __construct(private EntityManagerInterface $entityManager, private NotificationRuleListenerInterface $notificationRuleListener)
    {
    }

    /**
     * @BeforeSuite
     */
    public static function setupPimcore(): void
    {
        if (getenv('CORESHOP_SKIP_DB_SETUP')) {
            return;
        }

        Setup::setupCoreShop();
    }

    /**
     * @BeforeScenario
     */
    public function purgeIndexTables(): void
    {
        $connection = $this->entityManager->getConnection();
        $schemaManager = $connection->getSchemaManager();

        $tables = $schemaManager->listTableNames();
        $views = $schemaManager->listViews();

        foreach ($tables as $tbl) {
            if (str_starts_with($tbl, 'coreshop_index_mysql_')) {
                $schemaManager->dropTable($tbl);
            }
        }

        foreach ($views as $view) {
            if (str_starts_with($view->getName(), 'coreshop_index_mysql_')) {
                $schemaManager->dropView($view->getName());
            }
        }
    }

    /**
     * @BeforeScenario
     */
    public function clearNotificationRuleListener(): void
    {
        $this->notificationRuleListener->clear();
    }
}
