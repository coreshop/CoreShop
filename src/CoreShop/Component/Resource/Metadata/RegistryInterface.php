<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

    public function add(MetadataInterface $metadata): void;

    /**
     * @param string $alias
     */
    public function addFromAliasAndConfiguration($alias, array $configuration): void;
}
