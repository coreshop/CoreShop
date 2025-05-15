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

final class Registry implements RegistryInterface
{
    private array $metadata = [];

    public function getAll(): array
    {
        return $this->metadata;
    }

    public function get($alias): MetadataInterface
    {
        if (!array_key_exists($alias, $this->metadata)) {
            throw new \InvalidArgumentException(sprintf('Model "%s" does not exist.', $alias));
        }

        return $this->metadata[$alias];
    }

    public function getByClass($className): MetadataInterface
    {
        foreach ($this->metadata as $metadata) {
            if ($className === $metadata->getClass('model')) {
                return $metadata;
            }
        }

        throw new \InvalidArgumentException(sprintf('Model with class "%s" does not exist.', $className));
    }

    public function hasClass($className): bool
    {
        foreach ($this->metadata as $metadata) {
            if ($className === $metadata->getClass('model')) {
                return true;
            }
        }

        return false;
    }

    public function add(MetadataInterface $metadata): void
    {
        $this->metadata[$metadata->getAlias()] = $metadata;
    }

    public function addFromAliasAndConfiguration($alias, array $configuration): void
    {
        $this->add(Metadata::fromAliasAndConfiguration($alias, $configuration));
    }
}
