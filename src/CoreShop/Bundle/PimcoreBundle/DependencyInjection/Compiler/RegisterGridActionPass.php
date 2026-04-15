<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

class RegisterGridActionPass extends RegisterSimpleRegistryTypePass
{
    public const string GRID_ACTION_TAG = 'coreshop.grid.action';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.grid.action',
            'coreshop.grid.actions',
            self::GRID_ACTION_TAG,
        );
    }
}
