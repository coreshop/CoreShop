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

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class TokenBasedCustomerContext implements CustomerContextInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getCustomer(): CustomerInterface
    {
        if ($this->tokenStorage->getToken() instanceof TokenInterface && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
            return $this->tokenStorage->getToken()->getUser()->getCustomer();
        }

        throw new CustomerNotFoundException();
    }
}
