<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MakePimcoreServicesPublicPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loggerPrefixes = ['Pimcore\\Templating\\'];

        foreach ($loggerPrefixes as $loggerPrefix) {
            $serviceIds = array_filter($container->getServiceIds(), static function (string $id) use ($loggerPrefix) {
                return 0 === strpos($id, $loggerPrefix);
            });

            foreach ($serviceIds as $serviceId) {
                if ($container->hasAlias($serviceId)) {
                    $container->getAlias($serviceId)->setPublic(true);
                }

                if ($container->hasDefinition($serviceId)) {
                    $container
                        ->getDefinition($serviceId)
                        ->setPublic(true);
                }
            }
        }
    }
}
