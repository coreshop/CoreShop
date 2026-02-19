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

namespace CoreShop\Bundle\StorageListBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class RegisterStorageListPass extends PrioritizedCompositeServicePass
{
    public function __construct(
        string $serviceId,
        string $compositeId,
        string $tagName,
    ) {
        parent::__construct(
            $serviceId,
            $compositeId,
            $tagName,
            'addContext',
        );
    }
}
