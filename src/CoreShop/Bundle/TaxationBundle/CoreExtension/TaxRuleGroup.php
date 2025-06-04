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

namespace CoreShop\Bundle\TaxationBundle\CoreExtension;

use CoreShop\Bundle\ResourceBundle\CoreExtension\Select;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class TaxRuleGroup extends Select
{
    public string $fieldtype = 'coreShopTaxRuleGroup';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    protected function getRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.tax_rule_group');
    }

    protected function getModel(): string
    {
        return \Pimcore::getContainer()->getParameter('coreshop.model.tax_rule_group.class');
    }

    protected function getInterface(): string
    {
        return '\\' . TaxRuleGroupInterface::class;
    }

    protected function getNullable(): bool
    {
        return true;
    }
}
