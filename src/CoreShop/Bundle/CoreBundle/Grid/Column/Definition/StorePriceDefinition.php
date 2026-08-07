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

namespace CoreShop\Bundle\CoreBundle\Grid\Column\Definition;

use Pimcore\Bundle\StudioBackendBundle\Grid\Column\ColumnDefinitionInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\FrontendType;

final readonly class StorePriceDefinition implements ColumnDefinitionInterface
{
    public function getType(): string
    {
        return 'coreshop.storePrice';
    }

    public function getConfig(mixed $config): array
    {
        return [];
    }

    public function isSortable(): bool
    {
        return false;
    }

    public function isFilterable(): bool
    {
        return false;
    }

    public function getFrontendType(): string
    {
        return FrontendType::INPUT->value;
    }

    public function isExportable(): bool
    {
        return true;
    }
}
