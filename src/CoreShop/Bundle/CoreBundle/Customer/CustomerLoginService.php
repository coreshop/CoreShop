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

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Component\Core\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class CustomerLoginService implements CustomerLoginServiceInterface
{
    public function __construct(private TokenStorageInterface $securityTokenStorage)
    {
    }

    public function loginCustomer(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, null, 'coreshop_frontend', $user->getRoles());
        $this->securityTokenStorage->setToken($token);
    }
}
