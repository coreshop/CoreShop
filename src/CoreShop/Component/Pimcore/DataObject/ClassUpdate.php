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

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Model\DataObject;

class ClassUpdate extends AbstractDefinitionUpdate
{
    private ?DataObject\ClassDefinition $classDefinition;

    public function __construct(
        string $className,
    ) {
        parent::__construct();

        $this->classDefinition = DataObject\ClassDefinition::getByName($className);

        if (null === $this->classDefinition) {
            throw new ClassDefinitionNotFoundException(sprintf('ClassDefinition %s not found', $className));
        }

        $this->fieldDefinitions = $this->classDefinition->getFieldDefinitions();
        $this->jsonDefinition = json_decode(
            DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->classDefinition),
            true,
        );
        $this->originalJsonDefinition = $this->jsonDefinition;
    }

    public function save(): bool
    {
        return null !== DataObject\ClassDefinition\Service::importClassDefinitionFromJson(
            $this->classDefinition,
            json_encode($this->jsonDefinition),
            true,
        );
    }
}
