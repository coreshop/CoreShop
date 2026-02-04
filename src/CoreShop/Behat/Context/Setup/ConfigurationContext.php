<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Configuration\ConfigurationService;
use CoreShop\Component\Core\Model\StoreInterface;

final class ConfigurationContext implements Context
{
    public function __construct(
        private ConfigurationService $configurationService,
    ) {
    }

    /**
     * @Given configuration guest checkout is enabled
     * @Given /^configuration guest checkout is enabled for (store "[^"]+")$/
     */
    public function configurationGuestCheckoutIsEnabled(?StoreInterface $store = null): void
    {
        if ($store !== null) {
            $this->configurationService->setForStore('system.guest.checkout', true, $store);
        } else {
            $this->configurationService->set('system.guest.checkout', true);
        }
    }
}
