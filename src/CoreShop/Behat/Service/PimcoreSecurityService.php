<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    private CookieSetterInterface $cookieSetter;

    public function __construct(
        CookieSetterInterface $cookieSetter
    )
    {
        $this->cookieSetter = $cookieSetter;
    }

    public function logIn(User $user): void
    {
        Session::invalidate();
        Session::useSession(static function (AttributeBagInterface $adminSession) use ($user) {
            $adminSession->set('user', $user);
        });

        $this->cookieSetter->setCookie(Session::getSessionName(), Session::getSessionId());
    }

    public function logOut(): void
    {
        Session::invalidate();
    }
}
