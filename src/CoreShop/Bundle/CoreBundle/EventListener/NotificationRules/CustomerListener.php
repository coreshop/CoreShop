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

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Bundle\CustomerBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CustomerListener extends AbstractNotificationRuleListener
{
    /**
     * @param RequestPasswordChangeEvent $event
     */
    public function applyPasswordRequestResetRule(RequestPasswordChangeEvent $event)
    {
        $this->rulesProcessor->applyRules('user', $event->getCustomer(), [
            'type' => 'password-reset',
            'recipient' => $event->getCustomer()->getEmail(),
            'resetLink' => $event->getResetLink(),
            '_locale' => $this->shopperContext->getLocaleCode()
        ]);
    }

    /**
     * @param GenericEvent $event
     */
    public function applyRegisterCustomerRule(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), CustomerInterface::class);

        $user = $event->getSubject();

        if($user->getIsGuest() === true) {
            return;
        }

        $this->rulesProcessor->applyRules('user', $user, [
            'type' => 'register',
            'recipient' => $user->getEmail(),
            '_locale' => $this->shopperContext->getLocaleCode()
        ]);
    }
}
