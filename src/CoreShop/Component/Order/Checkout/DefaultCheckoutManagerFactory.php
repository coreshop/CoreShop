<?php

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistry;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class DefaultCheckoutManagerFactory implements CheckoutManagerFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $steps;

    /**
     * @var array
     */
    private $priorityMap;

    /**
     * @param ContainerInterface $steps
     * @param array              $priorityMap
     */
    public function __construct(ContainerInterface $steps, array $priorityMap)
    {
        $this->steps = $steps;
        $this->priorityMap = $priorityMap;
    }

    public function createCheckoutManager(CartInterface $cart)
    {
        $serviceRegistry = new PrioritizedServiceRegistry(CheckoutStepInterface::class, 'checkout-manager-steps');

        foreach ($this->priorityMap as $identifier => $priority) {
            $step = $this->steps->get($identifier);

            Assert::isInstanceOf($step, CheckoutStepInterface::class);

            if ($step instanceof OptionalCheckoutStepInterface && !$step->isRequired($cart)) {
                continue;
            }

            $serviceRegistry->register($identifier, $priority, $step);
        }

        return new CheckoutManager($serviceRegistry);
    }
}
