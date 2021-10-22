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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityService implements SecurityServiceInterface
{
    private string $sessionTokenVariable;

    public function __construct(
        private SessionInterface $session,
        private CookieSetterInterface $cookieSetter,
        private string $firewallContextName
    )
    {
        $this->sessionTokenVariable = sprintf('_security_%s', $firewallContextName);
    }

    public function logIn(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->firewallContextName, $user->getRoles());
        $this->setToken($token);
    }

    public function logOut(): void
    {
        $this->session->set($this->sessionTokenVariable, null);
        $this->session->save();

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    public function getCurrentToken(): TokenInterface
    {
        $serializedToken = $this->session->get($this->sessionTokenVariable);

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
        $this->session->set($this->sessionTokenVariable, $serializedToken);
        $this->session->save();
        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }
}
