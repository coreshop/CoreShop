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

namespace CoreShop\Behat\Service\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MonologChannelLoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $channelsToHide = [
            'event',
            'doctrine',
            'console',
            'cache',
            'pimcore',
        ];

        $monologHandlers = $container->getParameter('monolog.handlers_to_channels');

        foreach ($channelsToHide as $channelToHide) {
            $monologHandlers['monolog.handler.console']['elements'][] = $channelToHide;
        }

        $container->setParameter('monolog.handlers_to_channels', $monologHandlers);

        //$container->getDefinition('monolog.handler.console')->addMethodCall('pushHandler', array(new Reference($handler)));
    }
}
