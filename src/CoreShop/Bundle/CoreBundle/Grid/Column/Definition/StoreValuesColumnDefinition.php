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

/**
 * Registers the coreShopStoreValues field as a known grid column type.
 *
 * Without this, Pimcore's AdvancedColumnCollector lists the field as a Source Field option
 * but ResolverTypeGuesser fails to resolve it, leading to:
 *   "Key with Column Key: storeValues not found"
 *
 * Resolution itself is handled by the standard AdapterResolver which routes through the
 * existing StoreValuesAdapter::normalize() (already registered as data_adapter).
 */
final readonly class StoreValuesColumnDefinition implements ColumnDefinitionInterface
{
    public function getType(): string
    {
        return 'data-object.coreShopStoreValues';
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
        return false;
    }
}
