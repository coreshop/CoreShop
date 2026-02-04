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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartStockAvailability extends Constraint
{
    public string $message = 'coreshop.cart_item.not_sufficient_stock';

    public function validatedBy(): string
    {
        return 'coreshop_cart_stock_availability';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
