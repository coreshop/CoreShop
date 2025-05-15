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

namespace CoreShop\Bundle\PaymentBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class PaymentProvider extends Select
{
    /**
     * Static type of this element.
     */
    public string $fieldtype = 'coreShopPaymentProvider';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.payment_provider');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.payment_provider.class');
    }

    protected function getInterface(): string
    {
        return '\\' . PaymentProviderInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
