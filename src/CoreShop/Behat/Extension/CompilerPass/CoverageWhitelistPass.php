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

namespace CoreShop\Behat\Extension\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoverageWhitelistPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('behat.code_coverage.php_code_coverage_filter')) {
            return;
        }

        $filter = $container->getDefinition('behat.code_coverage.php_code_coverage_filter');

        $paths = ['lib/CoreShop/src/CoreShop/Component', 'lib/CoreShop/src/CoreShop/Bundle', 'vendor/coreshop/core-shop/src/CoreShop/Component', 'vendor/coreshop/core-shop/src/CoreShop/Bundle'];

        foreach ($paths as $path) {
            $filter->addMethodCall('addDirectoryToWhiteList', [$path]);
        }
    }
}
