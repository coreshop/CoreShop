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

namespace CoreShop\Behat\Service;

use CoreShop\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityService implements SecurityServiceInterface
{
    private string $sessionTokenVariable;

    public function __construct(
        private SessionFactory $sessionFactory,
        private RequestStack $requestStack,
        private CookieSetterInterface $cookieSetter,
        private string $firewallContextName
    ) {
        $this->sessionTokenVariable = sprintf('_security_%s', $firewallContextName);
    }

    public function logIn(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->firewallContextName, $user->getRoles());
        $this->setToken($token);
    }

    public function logOut(): void
    {
        $this->requestStack->getSession()->set($this->sessionTokenVariable, null);
        $this->requestStack->getSession()->save();

        $this->cookieSetter->setCookie($this->requestStack->getSession()->getName(), $this->requestStack->getSession()->getId());
    }

    public function getCurrentToken(): TokenInterface
    {
        $serializedToken = $this->requestStack->getSession()->get($this->sessionTokenVariable);

        if (null === $serializedToken) {
            throw new TokenNotFoundException();
        }

        return unserialize($serializedToken);
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->setToken($token);
    }

    private function setToken(TokenInterface $token): void
    {
        $serializedToken = serialize($token);
        $session = $this->sessionFactory->createSession();
        $session->set($this->sessionTokenVariable, $serializedToken);
        $session->save();

        $this->cookieSetter->setCookie($session->getName(), $session->getId());
    }
}
