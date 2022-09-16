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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\TestBundle\Service;

use Pimcore\Model\User;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

final class PimcoreSecurityService implements PimcoreSecurityServiceInterface
{
    public function __construct(private CookieSetterInterface $cookieSetter)
    {
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
