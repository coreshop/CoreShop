<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Kamil Wręczycki
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\RequestStack;


final class CustomerLoginService implements CustomerLoginServiceInterface
{
    /**
     * @var TokenStorage
     */
    private $securityTokenStorage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(TokenStorage $tokenStorage, RequestStack $requestStack)
    {
        $this->securityTokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function loginCustomer(CustomerInterface $customer)
    {
        $token = new UsernamePasswordToken($customer, null, 'coreshop_frontend', $customer->getRoles());
        $this->securityTokenStorage->setToken($token);
    }

    public function logoutCustomer()
    {
        $this->securityTokenStorage->setToken(null);
        $this->requestStack->getCurrentRequest()->getSession()->invalidate();
    }
}
