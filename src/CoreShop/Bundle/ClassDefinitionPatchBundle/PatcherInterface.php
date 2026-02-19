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

namespace CoreShop\Bundle\ClassDefinitionPatchBundle;

interface PatcherInterface
{
    public function getPatches(): array;

    public function old(PatchInterface $patch): array;

    public function new(PatchInterface $patch): array;

    public function patch(): void;

    public function patchClass(Patch $patch): void;
}
