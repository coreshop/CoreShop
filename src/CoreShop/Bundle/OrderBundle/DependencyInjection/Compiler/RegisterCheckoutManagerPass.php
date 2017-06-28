<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class RegisterCheckoutManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('coreshop_order.checkout_manager')) {
            return;
        }

        $checkoutManager = $container->getParameter('coreshop_order.checkout_manager');

        if (!$container->hasDefinition($checkoutManager)) {
            throw new ServiceNotFoundException(sprintf('Service Checkout-Manager \'%s\' not found in Container.', $checkoutManager));
        }

        $alias = new Alias($checkoutManager);
        $container->setAlias('coreshop.checkout_manager', $alias);
    }
}
