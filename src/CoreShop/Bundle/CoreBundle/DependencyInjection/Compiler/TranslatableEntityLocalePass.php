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

use CoreShop\Component\Core\Translation\TranslatableEntityPimcoreLocaleAssigner;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TranslatableEntityLocalePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $translatableEntityLocaleAssignerDefinition = new Definition(TranslatableEntityPimcoreLocaleAssigner::class);
        $translatableEntityLocaleAssignerDefinition->setPublic(true);
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference(LocaleContextInterface::class));
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference(TranslationLocaleProviderInterface::class));

        $container->setDefinition(TranslatableEntityLocaleAssignerInterface::class, $translatableEntityLocaleAssignerDefinition);
    }
}
