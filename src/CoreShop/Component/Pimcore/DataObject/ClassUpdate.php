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

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Model\DataObject;

class ClassUpdate extends AbstractDefinitionUpdate implements ClassUpdateRenameInterface
{
    private DataObject\ClassDefinition $classDefinition;
    private array $fieldsToRename = [];

    public function __construct(string $className)
    {
        $this->classDefinition = DataObject\ClassDefinition::getByName($className);

        if (null === $this->classDefinition) {
            throw new ClassDefinitionNotFoundException(sprintf('ClassDefinition %s not found', $className));
        }

        $this->fieldDefinitions = $this->classDefinition->getFieldDefinitions();
        $this->jsonDefinition = json_decode(DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->classDefinition), true);
    }

    public function save(): bool
    {
        foreach ($this->fieldsToRename as $from => $to) {
            $renamer = new ClassDefinitionFieldReNamer($this->classDefinition, $from, $to);
            $renamer->rename();
        }

        return null !== DataObject\ClassDefinition\Service::importClassDefinitionFromJson($this->classDefinition, json_encode($this->jsonDefinition), true);
    }

    public function renameField(string $fieldName, string $newFieldName): void
    {
        $this->findField(
            $fieldName,
            function (&$foundField, $index, &$parent) use ($fieldName, $newFieldName) {
                $this->fieldsToRename[$fieldName] = [
                    'newName' => $newFieldName,
                    'definition' => $foundField,
                ];
            }
        );
    }
}
