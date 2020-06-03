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

declare(strict_types=1);

namespace CoreShop\Behat\Service;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Pimcore\Bundle\AdminBundle\Session\Handler\AdminSessionHandler;
use Pimcore\Model\User;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class PimcoreSecurityService implements PimcoreSecurityServiceInterface
{
    private $session;
    private $cookieSetter;
    private $sessionTokenVariable;
    private $firewallContextName;

    public function __construct(
        SessionInterface $session,
        CookieSetterInterface $cookieSetter,
        string $firewallContextName
    )
    {
        $this->session = $session;
        $this->cookieSetter = $cookieSetter;
        $this->sessionTokenVariable = '_pimcore_admin';
        $this->firewallContextName = $firewallContextName;
    }

    public function logIn(User $user): void
    {
        Session::useSession(static function (AttributeBagInterface $adminSession) use ($user) {
            Session::regenerateId();
            $adminSession->set('user', $user);
        });

        $this->cookieSetter->setCookie(Session::getSessionName(), Session::getSessionId());

        Session::writeClose();
    }

    public function logOut(): void
    {
        Session::invalidate();
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

    private function setToken(TokenInterface $token)
    {
        $serializedToken = serialize($token);
        $this->session->set($this->sessionTokenVariable, $serializedToken);
        $this->session->save();

        $this->cookieSetter->setCookie($this->session->getName(), Session::getSessionId());
    }
}
