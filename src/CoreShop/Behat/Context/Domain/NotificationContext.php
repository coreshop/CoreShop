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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\NotificationRuleListenerInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class NotificationContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var NotificationRuleListenerInterface
     */
    private $notificationRuleListener;

    /**
     * @param SharedStorageInterface            $sharedStorage
     * @param NotificationRuleListenerInterface $notificationRuleListener
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        NotificationRuleListenerInterface $notificationRuleListener
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->notificationRuleListener = $notificationRuleListener;
    }

    /**
     * @Then /^the notification rule for "([^"]+)" should have been fired$/
     */
    public function thereShouldBeOneProductInTheOrder($type)
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
