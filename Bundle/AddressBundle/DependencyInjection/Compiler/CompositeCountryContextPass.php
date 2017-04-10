<?php

namespace CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeCountryContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.country',
            'coreshop.context.country.composite',
            'coreshop.context.country',
            'addContext'
        );
    }
}
