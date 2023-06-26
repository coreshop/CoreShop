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

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;

class Patcher implements PatcherInterface
{
    public function __construct(
        protected Patches $patches,
    ) {
    }

    public function getPatches(): array
    {
        return $this->patches->getPatches();
    }

    public function patch(): void
    {
        foreach ($this->patches->getPatches() as $patch) {
            $this->patchClass($patch);
        }
    }

    public function old(PatchInterface $patch): array
    {
        return $this->patchWithClassUpdate($patch)->getOriginalJsonDefinition();
    }

    public function new(PatchInterface $patch): array
    {
        return $this->patchWithClassUpdate($patch)->getJsonDefinition();
    }

    public function patchClass(Patch $patch): void
    {
        $this->patchWithClassUpdate($patch)->save();
    }

    protected function patchWithClassUpdate(PatchInterface $patch): ClassUpdate
    {
        $classUpdater = new ClassUpdate($patch->getClassName());

        if (null !== $patch->getParentClass()) {
            $classUpdater->setProperty('parentClass', $patch->getParentClass());
        }

        if (null !== $patch->getGroup()) {
            $classUpdater->setProperty('group', $patch->getGroup());
        }

        if (null !== $patch->getDescription()) {
            $classUpdater->setProperty('description', $patch->getDescription());
        }

        if (null !== $patch->getListingParentClass()) {
            $classUpdater->setProperty('listingParentClass', $patch->getListingUseTraits());
        }

        if (null !== $patch->getInterface()) {
            $interfaces = $classUpdater->getProperty('implementsInterfaces');

            if (null === $interfaces || !str_contains($interfaces, $patch->getInterface())) {
                if (!$interfaces) {
                    $interfaces = $patch->getInterface();
                } else {
                    $interfaces .= sprintf(',%s', $patch->getInterface());
                }

                $classUpdater->setProperty('implementsInterfaces', $interfaces);
            }
        }

        if (null !== $patch->getUseTraits()) {
            $traits = $classUpdater->getProperty('useTraits');

            if (null === $traits || !str_contains($traits, $patch->getUseTraits())) {
                if (!$traits) {
                    $traits = $patch->getUseTraits();
                } else {
                    $traits .= sprintf(',%s', $patch->getUseTraits());
                }

                $classUpdater->setProperty('useTraits', $traits);
            }
        }

        if (null !== $patch->getListingParentClass()) {
            $traits = $classUpdater->getProperty('listingParentClass');

            if (null === $traits || !str_contains($traits, $patch->getListingUseTraits())) {
                if (!$traits) {
                    $traits = $patch->getListingUseTraits();
                } else {
                    $traits .= sprintf(',%s', $patch->getListingUseTraits());
                }

                $classUpdater->setProperty('listingParentClass', $traits);
            }
        }

        foreach ($patch->getFields() as $field) {
            if ($classUpdater->hasField($field->getFieldName())) {
                $classUpdater->replaceField($field->getFieldName(), $field->getDefinition());
            } elseif ($field->getBefore()) {
                $classUpdater->insertFieldBefore($field->getBefore(), $field->getDefinition());
            } elseif ($field->getAfter()) {
                $classUpdater->insertFieldAfter($field->getAfter(), $field->getDefinition());
            } else {
                $classUpdater->insertField($field->getDefinition());
            }
        }

        return $classUpdater;
    }
}
