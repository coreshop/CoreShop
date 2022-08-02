<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ThemeBundle\Service\CompositeThemeResolver;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;
use Sylius\Bundle\ThemeBundle\Translation\ThemeAwareTranslator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveThemeAwareTranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(ThemeAwareTranslator::class)) {
            return;
        }

        $container->removeDefinition(ThemeAwareTranslator::class);
    }
}
