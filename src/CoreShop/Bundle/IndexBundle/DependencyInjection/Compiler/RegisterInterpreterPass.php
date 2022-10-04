<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

class RegisterInterpreterPass extends RegisterRegistryTypePass
{
    public const INDEX_INTERPRETER_TAG = 'coreshop.index.interpreter';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.index.interpreter',
            'coreshop.form_registry.index.interpreter',
            'coreshop.index.interpreters',
            self::INDEX_INTERPRETER_TAG,
        );
    }
}
