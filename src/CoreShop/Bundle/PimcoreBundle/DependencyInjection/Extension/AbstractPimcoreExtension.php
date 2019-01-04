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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

abstract class AbstractPimcoreExtension extends Extension
{
    /**
     * @param string           $applicationName
     * @param array            $bundleResources
     * @param ContainerBuilder $container
     */
    protected function registerPimcoreResources($applicationName, $bundleResources, ContainerBuilder $container)
    {
        $resourceTypes = ['js', 'css', 'editmode_js', 'editmode_css'];

        foreach ($resourceTypes as $resourceType) {
            $applicationParameter = sprintf('%s.pimcore.admin.%s', $applicationName, $resourceType);
            //$aliasParameter = sprintf('%s.pimcore.admin.%s', $this->getAlias(), $resourceType);
            $globalParameter = sprintf('coreshop.all.pimcore.admin.%s', $resourceType);

            $parameters = [
                $applicationParameter,
                $globalParameter,
            ];

            foreach ($parameters as $containerParameter) {
                $resources = [];
                $bundleTypeResources = [];

                if (array_key_exists($resourceType, $bundleResources)) {
                    $bundleTypeResources = array_values($bundleResources[$resourceType]);
                }

                if ($container->hasParameter($containerParameter)) {
                    $resources = $container->getParameter($containerParameter);
                }

                $container->setParameter($containerParameter, array_merge($resources, $bundleTypeResources));
            }
        }
    }
}
