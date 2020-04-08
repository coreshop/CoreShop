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

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\Setup;
use CoreShop\Bundle\CoreBundle\Test\Service\NotificationRuleListenerInterface;
use CoreShop\Bundle\NotificationBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CoreShopSetupContext implements Context
{
    private $entityManager;
    private $notificationRuleListener;
    private $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRuleListenerInterface $notificationRuleListener,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->notificationRuleListener = $notificationRuleListener;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @BeforeSuite
     */
    public static function setupPimcore()
    {
        if (getenv('CORESHOP_SKIP_DB_SETUP')) {
            return;
        }

        Setup::setupCoreShop();
    }

    /**
     * @BeforeScenario
     */
    public function purgeIndexTables()
    {
        $connection = $this->entityManager->getConnection();
        $schemaManager = $connection->getSchemaManager();

        $tables = $schemaManager->listTableNames();
        $views = $schemaManager->listViews();

        foreach ($tables as $tbl) {
            if (strpos($tbl, 'coreshop_index_mysql_') === 0) {
                $schemaManager->dropTable($tbl);
            }
        }

        foreach ($views as $view) {
            if (strpos($view->getName(), 'coreshop_index_mysql_') === 0) {
                $schemaManager->dropView($view->getName());
            }
        }
    }

    /**
     * @BeforeScenario
     */
    public function clearNotificationRuleListener()
    {
        $this->notificationRuleListener->clear();
    }
}
