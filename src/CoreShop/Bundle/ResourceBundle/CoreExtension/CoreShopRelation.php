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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class CoreShopRelation extends Data\ManyToOneRelation
{
    public string|null $stack = null;

    public bool $returnConcrete = false;

    public bool $objectsAllowed = true;

    public array $classes = [];

    public function getFieldType(): string
    {
        return 'coreShopRelation';
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

    public function getReturnConcrete(): bool
    {
        return $this->returnConcrete;
    }

    public function setReturnConcrete(bool $returnConcrete): void
    {
        $this->returnConcrete = $returnConcrete;
    }

    protected function getCoreShopPimcoreClasses()
    {
        return \Pimcore::getContainer()->getParameter('coreshop.all.stack.pimcore_class_names');
    }

    public function getParameterTypeDeclaration(): ?string
    {
        if ($this->getReturnConcrete()) {
            return '?\\' . Concrete::class;
        }

        /**
         * @var array $stack
         */
        $stack = \Pimcore::getContainer()->getParameter('coreshop.all.stack');

        return '?\\' . $stack[$this->stack];
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    public function getPhpdocInputType(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    public function getPhpdocReturnType(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    /**
     * @return $this
     */
    public function setClasses(array $classes): static
    {
        if (null === $this->stack) {
            return $this;
        }

        parent::setClasses(Element\Service::fixAllowedTypes($this->getCoreShopPimcoreClasses()[$this->stack], 'classes'));

        return $this;
    }

    public static function __set_state(array $data): static
    {
        $obj = parent::__set_state($data);
        $obj->classes = $obj->getClasses();
        $obj->objectsAllowed = true;

        return $obj;
    }
}
