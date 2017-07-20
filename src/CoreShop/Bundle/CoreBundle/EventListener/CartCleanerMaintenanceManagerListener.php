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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\OrderBundle\Cart\Maintenance\CleanupInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Pimcore\Event\System\MaintenanceEvent;
use Pimcore\Model\Schedule\Maintenance\Job;

final class CartCleanerMaintenanceManagerListener
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var CleanupInterface
     */
    private $cartCleanup;

    /**
     * @param ConfigurationServiceInterface $configurationService
     * @param CleanupInterface $cartCleanup
     */
    public function __construct(ConfigurationServiceInterface $configurationService, CleanupInterface $cartCleanup)
    {
        $this->configurationService = $configurationService;
        $this->cartCleanup = $cartCleanup;
    }

    /**
     * @param MaintenanceEvent $maintenanceEvent
     */
    public function registerCartCleanup(MaintenanceEvent $maintenanceEvent)
    {
        $lastMaintenance = $this->configurationService->get('system.cart.cleanup.last_run');

        if (is_null($lastMaintenance)) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $manager = $maintenanceEvent->getManager();

            $manager->registerJob(new Job('coreshop.cart.cleanup', [$this->cartCleanup, 'cleanup']));

             $this->configurationService->set('system.cart.cleanup.last_run', time());
        }
    }
}