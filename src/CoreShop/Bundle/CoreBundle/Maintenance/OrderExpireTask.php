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

namespace CoreShop\Bundle\CoreBundle\Maintenance;

use CoreShop\Bundle\OrderBundle\Expiration\OrderExpirationInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Pimcore\Maintenance\TaskInterface;

final class OrderExpireTask implements TaskInterface
{
    public function __construct(private ConfigurationServiceInterface $configurationService, private OrderExpirationInterface $proposalExpiration, private string $type, private int $days = 0, private array $params = [])
    {
    }

    public function execute(): void
    {
        $lastMaintenance = $this->configurationService->get(sprintf('system.%s.expire.last_run', $this->type));

        if (null === $lastMaintenance) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $this->proposalExpiration->expire($this->days, $this->params);

            $this->configurationService->set(sprintf('system.%s.expire.last_run', $this->type), time());
        }
    }
}
