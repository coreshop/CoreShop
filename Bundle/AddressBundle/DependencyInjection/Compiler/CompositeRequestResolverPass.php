<?php

namespace CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.country.request_based.resolver',
            'coreshop.context.country.request_based.resolver.composite',
            'coreshop.context.country.request_based.resolver',
            'addResolver'
        );
    }
}
