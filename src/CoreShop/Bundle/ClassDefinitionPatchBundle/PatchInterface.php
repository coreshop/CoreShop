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

interface PatchInterface
{
    public function getClassName(): string;

    public function getInterface(): ?string;

    public function getParentClass(): ?string;

    public function getGroup(): ?string;

    public function getDescription(): ?string;

    public function getListingParentClass(): ?string;

    public function getUseTraits(): ?string;

    public function getListingUseTraits(): ?string;

    /**
     * @return PatchField[]|null
     */
    public function getFields(): ?array;
}
