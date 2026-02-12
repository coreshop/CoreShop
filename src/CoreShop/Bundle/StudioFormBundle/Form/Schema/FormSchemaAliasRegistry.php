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

namespace CoreShop\Bundle\StudioFormBundle\Form\Schema;

final class FormSchemaAliasRegistry
{
    /** @var array<string, string> */
    private array $aliases = [];

    /**
     * @param array<string, string> $aliases
     */
    public function __construct(array $aliases = [])
    {
        $this->aliases = $aliases;
    }

    public function addAlias(string $alias, string $formTypeClass): void
    {
        $this->aliases[$alias] = $formTypeClass;
    }

    public function resolve(string $aliasOrClass): string
    {
        return $this->aliases[$aliasOrClass] ?? $aliasOrClass;
    }

    public function has(string $alias): bool
    {
        return isset($this->aliases[$alias]);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->aliases;
    }
}
