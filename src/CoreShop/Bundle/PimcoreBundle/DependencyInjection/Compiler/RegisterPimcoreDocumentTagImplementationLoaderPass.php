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

use Pimcore\Model\Document\Editable\Loader\EditableLoader;

final class RegisterPimcoreDocumentTagImplementationLoaderPass extends RegisterImplementationLoaderPass
{
    public function __construct(
        ) {
        parent::__construct(
            /** @psalm-suppress InternalClass */
            EditableLoader::class,
            'coreshop.pimcore.implementation_loader.document.editable',
        );
    }
}
