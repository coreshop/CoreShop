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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\StorageList\Context\StorageListNotFoundException;

class CartNotFoundException extends StorageListNotFoundException
{
    public function __construct(
        ?string $message = null,
        ?\Exception $previousException = null,
    ) {
        parent::__construct($message ?: 'CoreShop was not able to figure out the current cart.', $previousException);
    }
}
