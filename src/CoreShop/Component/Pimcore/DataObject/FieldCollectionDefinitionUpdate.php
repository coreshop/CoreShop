<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Model\DataObject;

class FieldCollectionDefinitionUpdate extends AbstractDefinitionUpdate
{
    /**
     * @var DataObject\Fieldcollection\Definition
     */
    private $fieldCollectionDefinition;

    public function __construct(string $fieldCollectionKey)
    {
        $this->fieldCollectionDefinition = DataObject\Fieldcollection\Definition::getByKey($fieldCollectionKey);

        if (is_null($this->fieldCollectionDefinition)) {
            throw new ClassDefinitionNotFoundException(sprintf('Fieldcollection Definition %s not found', $fieldCollectionKey));
        }

        $this->fieldDefinitions = $this->fieldCollectionDefinition->getFieldDefinitions();
        $this->jsonDefinition = json_decode(DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->fieldCollectionDefinition), true);
    }

    /**
     * {@inheritdoc}
     */
    public function save(): bool
    {
        return null !== DataObject\ClassDefinition\Service::importFieldCollectionFromJson($this->fieldCollectionDefinition, json_encode($this->jsonDefinition), true);
    }
}
