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

use Symfony\Component\Form\FormRegistryInterface;

final class BlockPrefixFormTypeRegistry
{
    /** @var array<string, string>|null */
    private ?array $blockPrefixMap = null;

    /**
     * @param string[] $formTypeClasses
     */
    public function __construct(
        private readonly FormRegistryInterface $formRegistry,
        private readonly array $formTypeClasses = [],
    ) {
    }

    public function resolve(string $blockPrefix): ?string
    {
        return $this->getMap()[$blockPrefix] ?? null;
    }

    public function has(string $blockPrefix): bool
    {
        return isset($this->getMap()[$blockPrefix]);
    }

    /**
     * @return array<string, string>
     */
    private function getMap(): array
    {
        if ($this->blockPrefixMap === null) {
            $this->blockPrefixMap = [];

            foreach (array_unique($this->formTypeClasses) as $class) {
                $resolvedType = $this->formRegistry->getType($class);
                $this->blockPrefixMap[$resolvedType->getBlockPrefix()] = $class;
            }
        }

        return $this->blockPrefixMap;
    }
}
