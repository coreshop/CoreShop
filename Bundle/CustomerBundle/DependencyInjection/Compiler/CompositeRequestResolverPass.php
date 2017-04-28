<?php

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.customer.request_based.resolver',
            'coreshop.context.customer.request_based.resolver.composite',
            'coreshop.context.customer.request_based.resolver',
            'addResolver'
        );
    }
}
