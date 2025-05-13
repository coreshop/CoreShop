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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class RegisterPimcoreDocumentTagPass extends RegisterSimpleRegistryTypePass
{
    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.pimcore.document.editable',
            'coreshop.registry.pimcore.document.editables',
            'coreshop.pimcore.document.editable',
        );
    }
}
