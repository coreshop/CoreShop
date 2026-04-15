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

namespace CoreShop\Bundle\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ValidAddressIdentifier extends Constraint
{
    public string $message = 'Address Identifier "%address_identifier%" is not valid.';

    public function validatedBy(): string
    {
        return 'coreshop_address_valid_identifier';
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
