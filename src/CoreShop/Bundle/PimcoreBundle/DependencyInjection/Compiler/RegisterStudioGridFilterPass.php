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

class RegisterStudioGridFilterPass extends RegisterSimpleRegistryTypePass
{
    public const string STUDIO_GRID_FILTER_TAG = 'coreshop.studio.grid.filter';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.studio.grid.filter',
            'coreshop.studio.grid.filters',
            self::STUDIO_GRID_FILTER_TAG,
        );
    }
}
