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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Configuration\ConfigurationService;
use CoreShop\Component\Core\Model\StoreInterface;

final class ConfigurationContext implements Context
{
    public function __construct(private ConfigurationService $configurationService)
    {
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
