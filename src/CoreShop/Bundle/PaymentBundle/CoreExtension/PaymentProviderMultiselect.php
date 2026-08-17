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

namespace CoreShop\Bundle\PaymentBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Multiselect;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class PaymentProviderMultiselect extends Multiselect
{
    public string $fieldtype = 'coreShopPaymentProviderMultiselect';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }
}
