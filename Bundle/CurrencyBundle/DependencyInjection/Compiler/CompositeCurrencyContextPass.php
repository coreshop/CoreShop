<?php

namespace CoreShop\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeCurrencyContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.currency',
            'coreshop.context.currency.composite',
            'coreshop.context.currency',
            'addContext'
        );
    }
}
