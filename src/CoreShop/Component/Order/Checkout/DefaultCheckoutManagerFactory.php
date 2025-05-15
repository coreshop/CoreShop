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

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistry;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class DefaultCheckoutManagerFactory implements CheckoutManagerFactoryInterface
{
    public function __construct(
        private ContainerInterface $steps,
        private array $priorityMap,
    ) {
    }

    public function createCheckoutManager(OrderInterface $cart): CheckoutManagerInterface
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
