<?php

namespace CoreShop\Component\Customer\Context\RequestBased;

use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class TokenBasedRequestResolver implements RequestResolverInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function findCustomer(Request $request)
    {
        if ($this->tokenStorage->getToken() instanceof TokenInterface && $this->tokenStorage->getToken()->getUser() instanceof CustomerInterface) {
            return $this->tokenStorage->getToken()->getUser();
        }

        throw new CustomerNotFoundException();
    }
}
