<?php

namespace CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.store.request_based.resolver',
            'coreshop.context.store.request_based.resolver.composite',
            'coreshop.context.store.request_based.resolver',
            'addResolver'
        );
    }
}
