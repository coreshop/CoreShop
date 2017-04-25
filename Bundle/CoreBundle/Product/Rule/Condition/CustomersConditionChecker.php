<?php

namespace CoreShop\Bundle\CoreBundle\Product\Rule\Condition;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CustomersConditionChecker implements ConditionCheckerInterface
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
    public function isValid($subject, array $configuration)
    {
        $customer = $this->tokenStorage->getToken() instanceof TokenInterface ? $this->tokenStorage->getToken()->getUser() : null;

        if (!$customer instanceof CustomerInterface) {
            return false;
        }

        return in_array($customer->getId(), $configuration['customers']);
    }
}
