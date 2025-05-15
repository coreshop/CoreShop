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

namespace CoreShop\Bundle\IndexBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Index\Model\FilterInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class Filter extends Select
{
    public string $fieldtype = 'coreShopFilter';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.filter');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.filter.class');
    }

    protected function getInterface(): string
    {
        return '\\' . FilterInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
