<?php

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeLocaleContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.locale',
            'coreshop.context.locale.composite',
            'coreshop.context.locale',
            'addContext'
        );
    }
}
