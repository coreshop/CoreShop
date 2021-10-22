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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\CoreBundle\Test\Service\NotificationRuleListenerInterface;
use Webmozart\Assert\Assert;

final class NotificationContext implements Context
{
    public function __construct(private NotificationRuleListenerInterface $notificationRuleListener)
    {
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
                $type
            )
        );
    }
}
