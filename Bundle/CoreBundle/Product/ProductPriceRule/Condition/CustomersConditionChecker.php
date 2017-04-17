<?php

namespace CoreShop\Bundle\CoreBundle\Product\ProductPriceRule\Condition;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

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
        Assert::isInstanceOf($subject, ProductInterface::class);

        $customer = $this->tokenStorage->getToken()->getUser();

        if (!$customer instanceof CustomerInterface) {
            return false;
        }

        return in_array($customer->getId(), $configuration['customers']);
    }
}
