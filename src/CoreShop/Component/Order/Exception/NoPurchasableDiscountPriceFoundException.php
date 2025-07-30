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

namespace CoreShop\Component\Order\Exception;

class NoPurchasableDiscountPriceFoundException extends \Exception
{
    public function __construct(
        string $calculatorClass,
        ?\Exception $previousException = null,
    ) {
        parent::__construct(sprintf('Price Calculator "%s" was not able to match a valid discount price.', $calculatorClass), 0, $previousException);
    }
}
