<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
