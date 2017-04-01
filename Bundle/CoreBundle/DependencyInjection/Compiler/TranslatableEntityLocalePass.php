<?php

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Core\Translation\TranslatableEntityPimcoreLocaleAssigner;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TranslatableEntityLocalePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $translatableEntityLocaleAssignerDefinition = new Definition(TranslatableEntityPimcoreLocaleAssigner::class);

        $container->setDefinition('coreshop.translatable_entity_locale_assigner', $translatableEntityLocaleAssignerDefinition);
    }
}
