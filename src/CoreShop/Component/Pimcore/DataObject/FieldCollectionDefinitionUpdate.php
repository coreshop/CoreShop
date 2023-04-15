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

class FieldCollectionDefinitionUpdate extends AbstractDefinitionUpdate
{
    private DataObject\Fieldcollection\Definition $fieldCollectionDefinition;

    public function __construct(
        string $fieldCollectionKey,
    ) {
        parent::__construct();

        $fieldCollectionDefinition = DataObject\Fieldcollection\Definition::getByKey($fieldCollectionKey);

        if (null === $fieldCollectionDefinition) {
            throw new ClassDefinitionNotFoundException(sprintf('Fieldcollection Definition %s not found', $fieldCollectionKey));
        }

        $this->fieldCollectionDefinition = $fieldCollectionDefinition;
        $this->fieldDefinitions = $this->fieldCollectionDefinition->getFieldDefinitions();
        /** @psalm-suppress InvalidArgument */
        $this->jsonDefinition = json_decode(DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->fieldCollectionDefinition), true);
    }

    public function save(): bool
    {
        return DataObject\ClassDefinition\Service::importFieldCollectionFromJson($this->fieldCollectionDefinition, json_encode($this->jsonDefinition), true);
    }
}
