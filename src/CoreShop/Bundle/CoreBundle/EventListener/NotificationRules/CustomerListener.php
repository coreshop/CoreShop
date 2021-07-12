<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Bundle\CoreBundle\Event\RequestNewsletterConfirmationEvent;
use CoreShop\Bundle\UserBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Core\Notification\Rule\Condition\User\UserTypeChecker;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CustomerListener extends AbstractNotificationRuleListener
{
    public function applyPasswordRequestResetRule(RequestPasswordChangeEvent $event): void
    {
        Assert::isInstanceOf($event->getUser(), UserInterface::class);

        /**
         * @var UserInterface $user
         */
        $user = $event->getUser();

        $params = $this->prepareUserParameters($user);
        $params = array_merge(
            $params,
            [
                'type' => UserTypeChecker::TYPE_PASSWORD_RESET,
                'resetLink' => $event->getResetLink(),
            ]
        );

        $this->rulesProcessor->applyRules('user', $user, $params);
    }

    public function applyRegisterCustomerRule(GenericEvent $event): void
    {
        Assert::isInstanceOf($event->getSubject(), CustomerInterface::class);

        /**
         * @var CustomerInterface $customer
         */
        $customer = $event->getSubject();

        if (null === $customer->getUser()) {
            return;
        }

        $params = $this->prepareUserParameters($customer->getUser());
        $params = array_merge(
            $params,
            [
                'type' => UserTypeChecker::TYPE_REGISTER,
            ]
        );

        $this->rulesProcessor->applyRules('user', $customer, $params);
    }

    public function applyNewsletterConfirmRequestRule(RequestNewsletterConfirmationEvent $event): void
    {
        Assert::isInstanceOf($event->getCustomer(), CustomerInterface::class);

        /**
         * @var CustomerInterface $customer
         */
        $customer = $event->getCustomer();

        if (null === $customer->getUser()) {
            return;
        }

        if (!$customer instanceof Concrete) {
            return;
        }

        $customer->setNewsletterToken(hash('md5', $customer->getId() . $customer->getEmail() . mt_rand() . time()));

        VersionHelper::useVersioning(
            function () use ($customer) {
                $customer->save();
            },
            false
        );

        $confirmLink = $event->getConfirmLink();
        $confirmLink = $confirmLink . (parse_url(
            $confirmLink,
            PHP_URL_QUERY
        ) ? '&' : '?') . 'token=' . $customer->getNewsletterToken();

        $params = $this->prepareUserParameters($customer->getUser());
        $params = array_merge(
            $params,
            [
                'type' => UserTypeChecker::TYPE_NEWSLETTER_DOUBLE_OPT_IN,
                'confirmLink' => $confirmLink,
                'token' => $customer->getNewsletterToken(),
            ]
        );

        $this->rulesProcessor->applyRules('user', $customer->getUser(), $params);
    }

    public function applyNewsletterConfirmed(GenericEvent $event): void
    {
        Assert::isInstanceOf($event->getSubject(), CustomerInterface::class);

        /**
         * @var CustomerInterface $customer
         */
        $customer = $event->getSubject();

        if (null === $customer->getUser()) {
            return;
        }

        $params = $this->prepareUserParameters($customer->getUser());
        $params = array_merge(
            $params,
            [
                'type' => UserTypeChecker::TYPE_NEWSLETTER_CONFIRMED,
            ]
        );

        $this->rulesProcessor->applyRules('user', $customer, $params);
    }

    private function prepareUserParameters(UserInterface $user): array
    {
        return [
            '_locale' => $this->shopperContext->getLocaleCode(),
            'recipient' => $user->getCustomer()->getEmail(),
            'gender' => $user->getCustomer()->getGender(),
            'firstname' => $user->getCustomer()->getFirstname(),
            'lastname' => $user->getCustomer()->getLastname(),
            'email' => $user->getCustomer()->getEmail(),
            'object' => $user->getCustomer(),
            'user' => $user,
            'store' => $this->shopperContext->hasStore() ? $this->shopperContext->getStore() : null,
        ];
    }
}
