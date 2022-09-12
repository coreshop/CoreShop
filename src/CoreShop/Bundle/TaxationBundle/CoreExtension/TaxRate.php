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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\TaxationBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Taxation\Model\TaxRateInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class TaxRate extends Select
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopTaxRate';

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.tax_rate');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.tax_rate.class');
    }

    protected function getInterface(): string
    {
        return '\\' . TaxRateInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
