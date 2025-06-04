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

namespace CoreShop\Bundle\CurrencyBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Currency\Model\CurrencyInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class Currency extends Select
{
    public string $fieldtype = 'coreShopCurrency';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.currency');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.currency.class');
    }

    protected function getInterface(): string
    {
        return '\\' . CurrencyInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
