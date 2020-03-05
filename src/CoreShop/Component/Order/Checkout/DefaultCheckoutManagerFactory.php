<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistry;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class DefaultCheckoutManagerFactory implements CheckoutManagerFactoryInterface
{
    private $steps;
    private $priorityMap;

    public function __construct(ContainerInterface $steps, array $priorityMap)
    {
        $this->steps = $steps;
        $this->priorityMap = $priorityMap;
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
