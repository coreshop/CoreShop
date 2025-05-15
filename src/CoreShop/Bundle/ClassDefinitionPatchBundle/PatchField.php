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

namespace CoreShop\Bundle\ClassDefinitionPatchBundle;

class PatchField implements PatchFieldInterface
{
    public function __construct(
        protected string $fieldName,
        protected ?string $after,
        protected ?string $before,
        protected ?array $definition,
        protected bool $replace = true,
    ) {
        if (null === $this->definition) {
            $this->definition = [];
        }

        $this->definition['name'] = $this->fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getAfter(): ?string
    {
        return $this->after;
    }

    public function getBefore(): ?string
    {
        return $this->before;
    }

    public function getDefinition(): ?array
    {
        return $this->definition;
    }

    public function isReplace(): bool
    {
        return $this->replace;
    }
}
