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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

class RegisterGetterPass extends RegisterRegistryTypePass
{
    public const string INDEX_GETTER_TAG = 'coreshop.index.getter';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.index.getter',
            'coreshop.form_registry.index.getter',
            'coreshop.index.getters',
            self::INDEX_GETTER_TAG,
        );
    }
}
