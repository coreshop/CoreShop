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

class Patches implements PatchesInterface
{
    public function __construct(
        protected array $patches = [],
    ) {
    }

    public function getPatches(): array
    {
        return $this->patches;
    }

    public function hasPatch(string $className): bool
    {
        return array_key_exists($className, $this->patches);
    }

    public function getPatch(string $className)
    {
        if (!array_key_exists($className, $this->patches)) {
            throw new \InvalidArgumentException('Patch for class ' . $className . ' not found');
        }

        return $this->patches[$className];
    }
}
