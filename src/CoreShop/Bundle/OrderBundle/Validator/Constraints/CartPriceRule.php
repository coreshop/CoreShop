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

namespace CoreShop\Bundle\OrderBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CartPriceRule extends Constraint
{
    public string $message = 'Voucher "%rule%" is not valid anymore.';

    public function validatedBy(): string
    {
        return 'coreshop_cart_rule_valid';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
