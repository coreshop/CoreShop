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

    /** @var string[] */
    private array $additionalFormTypeClasses = [];

    /**
     * @param string[] $formTypeClasses
     */
    public function __construct(
        private readonly FormRegistryInterface $formRegistry,
        private readonly array $formTypeClasses = [],
    ) {
    }

    /**
     * Register an additional form type class (used by compiler passes for rule element form types).
     */
    public function addFormTypeClass(string $formTypeClass): void
    {
        $this->additionalFormTypeClasses[] = $formTypeClass;
        $this->blockPrefixMap = null;
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
            $allClasses = array_unique(array_merge($this->formTypeClasses, $this->additionalFormTypeClasses));

            foreach ($allClasses as $class) {
                $resolvedType = $this->formRegistry->getType($class);
                $this->blockPrefixMap[$resolvedType->getBlockPrefix()] = $class;
            }
        }

        return $this->blockPrefixMap;
    }
}
