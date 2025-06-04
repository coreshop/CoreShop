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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class TokenBasedCustomerContext implements CustomerContextInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function getCustomer(): CustomerInterface
    {
        if ($this->tokenStorage->getToken() instanceof TokenInterface && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
            /**
             * @var UserInterface $user
             */
            $user = $this->tokenStorage->getToken()->getUser();

            if (null === $user->getCustomer()) {
                throw new CustomerNotFoundException();
            }

            return $user->getCustomer();
        }

        throw new CustomerNotFoundException();
    }
}
