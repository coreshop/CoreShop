<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\CoreBundle\Test\Service\NotificationRuleListenerInterface;
use Webmozart\Assert\Assert;

final class NotificationContext implements Context
{
    public function __construct(
        private NotificationRuleListenerInterface $notificationRuleListener,
    ) {
    }

    /**
     * @Then /^the notification rule for "([^"]+)" should have been fired$/
     */
    public function thereShouldBeOneProductInTheOrder($type): void
    {
        Assert::true(
            $this->notificationRuleListener->hasBeenFired($type),
            sprintf(
                'Expected that the notification rule for type "%s" has been fired.',
                $type,
            ),
        );
    }
}
