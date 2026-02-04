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

namespace CoreShop\Component\Address\Context;

class CountryNotFoundException extends \RuntimeException
{
    public function __construct(
        ?\Exception $previousException = null,
    ) {
        parent::__construct('Country could not be found!', 0, $previousException);
    }
}
