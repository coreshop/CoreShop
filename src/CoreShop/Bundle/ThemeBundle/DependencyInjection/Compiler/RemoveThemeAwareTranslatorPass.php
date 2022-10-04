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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Translation\ThemeAwareTranslator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveThemeAwareTranslatorPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(ThemeAwareTranslator::class)) {
            return;
        }

        $container->removeDefinition(ThemeAwareTranslator::class);
    }
}
