<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Component\Core\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class CustomerLoginService implements CustomerLoginServiceInterface
{
    private TokenStorageInterface $securityTokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->securityTokenStorage = $tokenStorage;
    }

    public function loginCustomer(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, null, 'coreshop_frontend', $user->getRoles());
        $this->securityTokenStorage->setToken($token);
    }
}
