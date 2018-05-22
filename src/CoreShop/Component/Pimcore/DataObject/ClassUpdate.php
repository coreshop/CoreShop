<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\ClassDefinitionNotFoundException;
use Pimcore\Model\DataObject;

class ClassUpdate extends AbstractDefinitionUpdate
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var DataObject\ClassDefinition
     */
    private $classDefinition;

    /**
     * @param $className
     * @throws ClassDefinitionNotFoundException
     */
    public function __construct($className)
    {
        $this->className = $className;
        $this->classDefinition = DataObject\ClassDefinition::getByName($className);

        if (is_null($this->classDefinition)) {
            throw new ClassDefinitionNotFoundException(sprintf('ClassDefinition %s not found', $className));
        }

        $this->fieldDefinitions = $this->classDefinition->getFieldDefinitions();
        $this->jsonDefinition = json_decode(DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->classDefinition), true);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return DataObject\ClassDefinition\Service::importClassDefinitionFromJson($this->classDefinition, json_encode($this->jsonDefinition), true);
    }
}

class_alias(ClassUpdate::class, 'CoreShop\Component\Pimcore\ClassUpdate');