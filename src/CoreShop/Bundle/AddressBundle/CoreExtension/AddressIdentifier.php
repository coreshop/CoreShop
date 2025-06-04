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

namespace CoreShop\Bundle\AddressBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Address\Repository\AddressIdentifierRepositoryInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class AddressIdentifier extends Select
{
    public string $fieldtype = 'coreShopAddressIdentifier';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    protected function getRepository(): AddressIdentifierRepositoryInterface
    {
        return \Pimcore::getContainer()->get('coreshop.repository.address_identifier');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.address_identifier.class');
    }

    protected function getInterface(): string
    {
        return '\\' . AddressIdentifierInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
