<?php

namespace CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeStoreContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.store',
            'coreshop.context.store.composite',
            'coreshop.context.store',
            'addContext'
        );
    }
}
