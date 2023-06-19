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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class CoreShopRelations extends Data\ManyToManyRelation
{
    public string|null $stack;

    public function getFieldType(): string
    {
        return 'coreShopRelations';
    }

    public function getStack()
    {
        return $this->stack;
    }

    public function setStack($stack): void
    {
        $this->stack = $stack;

        $this->setClasses([]);
    }

    public function enrichLayoutDefinition($object, $context)
    {
        $this->classes = $this->getClasses();
    }

    protected function getCoreShopPimcoreClasses()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.all.stack.pimcore_class_names');
    }

    public function getParameterTypeDeclaration(): ?string
    {
        return '?array';
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return '?array';
    }

    public function getPhpdocInputType(): ?string
    {
        /**
         * @var array $stack
         */
        $stack = \Pimcore::getContainer()->getParameter('coreshop.all.stack');

        return '?\\' . $stack[$this->stack] . '[]';
    }

    public function getPhpdocReturnType(): ?string
    {
        /**
         * @var array $stack
         */
        $stack = \Pimcore::getContainer()->getParameter('coreshop.all.stack');

        return '?\\' . $stack[$this->stack] . '[]';
    }

    public function getClasses(): array
    {
        if (null === $this->stack) {
            return [];
        }

        $classes = $this->getCoreShopPimcoreClasses()[$this->stack];
        $return = [];

        foreach ($classes as $cl) {
            $return[] = [
                'classes' => $cl,
            ];
        }

        return $return;
    }

    public function getObjectsAllowed(): bool
    {
        return true;
    }

    public function getDocumentsAllowed(): bool
    {
        return false;
    }

    public function getDocumentTypes(): array
    {
        return [];
    }

    public function getAssetsAllowed(): bool
    {
        return false;
    }

    public function getAssetTypes(): array
    {
        return [];
    }
}
