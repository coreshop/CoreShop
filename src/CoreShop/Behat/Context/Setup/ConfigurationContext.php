<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Configuration\ConfigurationService;
use CoreShop\Component\Core\Model\StoreInterface;

final class ConfigurationContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ConfigurationService $configurationService;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ConfigurationService $configurationService
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->configurationService = $configurationService;
    }

    /**
     * @Given configuration guest checkout is enabled
     * @Given /^configuration guest checkout is enabled for (store "[^"]+")$/
     */
    public function configurationGuestCheckoutIsEnabled(?StoreInterface $store = null): void
    {
        if ($store !== null) {
            $this->configurationService->setForStore('system.guest.checkout', true, $store);
        }
        else {
            $this->configurationService->set('system.guest.checkout', true);
        }
    }
}
