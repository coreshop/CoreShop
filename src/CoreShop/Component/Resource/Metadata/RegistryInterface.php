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

namespace CoreShop\Component\Resource\Metadata;

interface RegistryInterface
{
    /**
     * @return MetadataInterface[]
     */
    public function getAll(): array;

    /**
     * @param string $alias
     */
    public function get($alias): MetadataInterface;

    /**
     * @param string $className
     */
    public function getByClass($className): MetadataInterface;

    public function hasClass($className): bool;

    public function add(MetadataInterface $metadata): void;

    /**
     * @param string $alias
     */
    public function addFromAliasAndConfiguration($alias, array $configuration): void;
}
