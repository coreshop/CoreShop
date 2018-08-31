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

namespace CoreShop\Bundle\CoreBundle\EventListener\Order\Expire;

use CoreShop\Bundle\OrderBundle\Expiration\ProposalExpirationInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Pimcore\Event\System\MaintenanceEvent;
use Pimcore\Model\Schedule\Maintenance\Job;

final class ProposalExpireMaintenanceManagerListener
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var ProposalExpirationInterface
     */
    private $proposalExpiration;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $days;

    /**
     * @var array
     */
    private $params;

    /**
     * @param ConfigurationServiceInterface $configurationService
     * @param ProposalExpirationInterface $proposalExpiration
     * @param int $days
     * @param array $params
     */
    public function __construct(ConfigurationServiceInterface $configurationService, ProposalExpirationInterface $proposalExpiration, $type, $days = 0, $params = [])
    {
        $this->configurationService = $configurationService;
        $this->proposalExpiration = $proposalExpiration;
        $this->type = $type;
        $this->days = $days;
        $this->params = $params;
    }

    /**
     * @param MaintenanceEvent $maintenanceEvent
     */
    public function registerExpire(MaintenanceEvent $maintenanceEvent)
    {
        $lastMaintenance = $this->configurationService->get(sprintf('system.%s.expire.last_run', $this->type));

        if (is_null($lastMaintenance)) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $manager = $maintenanceEvent->getManager();

            $manager->registerJob(new Job(sprintf('coreshop.%s.expire', $this->type), [$this->proposalExpiration, 'expire'], [$this->days, $this->params]));

            $this->configurationService->set(sprintf('system.%s.expire.last_run', $this->type), time());
        }
    }
}