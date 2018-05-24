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

use CoreShop\Bundle\CoreBundle\Event\RequestNewsletterConfirmationEvent;
use CoreShop\Bundle\CustomerBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Notification\Rule\Condition\User\UserTypeChecker;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Pimcore\Model\DataObject\Concrete;
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
            'type' => UserTypeChecker::TYPE_PASSWORD_RESET,
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

        /**
         * @var $user CustomerInterface
         */
        $user = $event->getSubject();

        if ($user->getIsGuest() === true) {
            return;
        }

        $this->rulesProcessor->applyRules('user', $user, [
            'type' => UserTypeChecker::TYPE_REGISTER,
            'recipient' => $user->getEmail(),
            '_locale' => $this->shopperContext->getLocaleCode()
        ]);
    }

    /**
     * @param RequestNewsletterConfirmationEvent $event
     */
    public function applyNewsletterConfirmRequestRule(RequestNewsletterConfirmationEvent $event)
    {
        Assert::isInstanceOf($event->getCustomer(), CustomerInterface::class);

        /**
         * @var $user CustomerInterface
         */
        $user = $event->getCustomer();

        if ($user->getIsGuest() === true) {
            return;
        }

        if (!$user instanceof Concrete) {
            return;
        }

        $user->setNewsletterToken(hash('md5', $user->getId().$user->getEmail().mt_rand().time()));

        VersionHelper::useVersioning(function() use ($user) {
            $user->save();
        }, false);

        $confirmLink = $event->getConfirmLink();
        $confirmLink = $confirmLink.(parse_url($confirmLink, PHP_URL_QUERY) ? '&' : '?').'token='.$user->getNewsletterToken();

        $this->rulesProcessor->applyRules('user', $user, [
            'type' => UserTypeChecker::TYPE_NEWSLETTER_DOUBLE_OPT_IN,
            'recipient' => $user->getEmail(),
            '_locale' => $this->shopperContext->getLocaleCode(),
            'gender' => $user->getGender(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'token' => $user->getNewsletterToken(),
            'object' => $user,
            'confirmLink' => $confirmLink
        ]);
    }

    /**
     * @param GenericEvent $event
     */
    public function applyNewsletterConfirmed(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), CustomerInterface::class);

        /**
         * @var $user CustomerInterface
         */
        $user = $event->getSubject();

        if ($user->getIsGuest() === true) {
            return;
        }

        $this->rulesProcessor->applyRules('user', $user, [
            'type' => UserTypeChecker::TYPE_NEWSLETTER_CONFIRMED,
            'recipient' => $user->getEmail(),
            '_locale' => $this->shopperContext->getLocaleCode()
        ]);
    }
}
