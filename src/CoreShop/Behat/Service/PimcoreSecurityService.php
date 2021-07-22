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

use Pimcore\Model\User;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

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
