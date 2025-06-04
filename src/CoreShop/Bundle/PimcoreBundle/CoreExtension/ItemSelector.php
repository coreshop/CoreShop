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

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement, MissingConstructor
 */
class ItemSelector extends DynamicDropdownMultiple
{
    public string $fieldtype = 'coreShopItemSelector';

    public function getFieldType(): string
    {
        return $this->fieldtype;
    }

    public function getObjectsAllowed(): bool
    {
        return true;
    }
}
