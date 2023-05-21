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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ClassDefinitionPatchBundle;

class Patch implements PatchInterface
{
    public function __construct(
        protected string $className,
        protected ?string $interface,
        protected ?string $parentClass,
        protected ?string $group,
        protected ?string $description,
        protected ?string $listingParentClass,
        protected ?string $useTraits,
        protected ?string $listingUseTraits,
        protected ?array $fields,
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getInterface(): ?string
    {
        return $this->interface;
    }

    public function getParentClass(): ?string
    {
        return $this->parentClass;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getListingParentClass(): ?string
    {
        return $this->listingParentClass;
    }

    public function getUseTraits(): ?string
    {
        return $this->useTraits;
    }

    public function getListingUseTraits(): ?string
    {
        return $this->listingUseTraits;
    }

    /**
     * @return PatchField[]|null
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }
}
