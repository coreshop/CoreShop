<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\Metadata;

interface RegistryInterface
{
    /**
     * @return MetadataInterface[]
     */
    public function getAll();

    /**
     * @param string $alias
     *
     * @return MetadataInterface
     */
    public function get($alias);

    /**
     * @param string $className
     *
     * @return MetadataInterface
     */
    public function getByClass($className);

    /**
     * @param MetadataInterface $metadata
     */
    public function add(MetadataInterface $metadata);

    /**
     * @param string $alias
     * @param array  $configuration
     */
    public function addFromAliasAndConfiguration($alias, array $configuration);
}
