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
use CoreShop\Behat\Service\NotificationRuleListenerInterface;
use CoreShop\Bundle\NotificationBundle\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CoreShopSetupContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var NotificationRuleListenerInterface
     */
    private $notificationRuleListener;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface            $entityManager
     * @param NotificationRuleListenerInterface $notificationRuleListener
     * @param EventDispatcherInterface          $eventDispatcher
     */
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

        \CoreShop\Test\Setup::setupCoreShop();
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
            if (0 === strpos($tbl, 'coreshop_index_mysql_')) {
                $schemaManager->dropTable($tbl);
            }
        }

        foreach ($views as $view) {
            if (0 === strpos($view->getName(), 'coreshop_index_mysql_')) {
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

        $function = function (GenericEvent $event) {
            $this->notificationRuleListener->applyNewFired($event->getSubject());
        };

        $this->eventDispatcher->removeListener(Events::PRE_APPLY, $function);
        $this->eventDispatcher->addListener(Events::PRE_APPLY, $function);
    }
}
