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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use SeoBundle\MetaData\MetaDataProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SeoBundlePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(MetaDataProviderInterface::class)) {
            return;
        }

        if ($container->hasAlias(MetaDataProviderInterface::class)) {
            $container
                ->getAlias(MetaDataProviderInterface::class)
                ->setPublic(true);
        }

        $container
            ->findDefinition(MetaDataProviderInterface::class)
            ->setPublic(true);
    }
}
