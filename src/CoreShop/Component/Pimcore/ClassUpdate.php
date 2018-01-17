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

namespace CoreShop\Component\Pimcore;

use Pimcore\Model\DataObject;

class ClassUpdate implements ClassUpdateInterface
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
     * @var array
     */
    private $jsonDefinition;

    /**
     * @var array
     */
    private $classFieldDefinitions;

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

        $this->classFieldDefinitions = $this->classDefinition->getFieldDefinitions();
        $this->jsonDefinition = json_decode(DataObject\ClassDefinition\Service::generateClassDefinitionJson($this->classDefinition), true);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return DataObject\ClassDefinition\Service::importClassDefinitionFromJson($this->classDefinition, json_encode($this->jsonDefinition), true);
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($fieldName)
    {
        return array_key_exists($fieldName, $this->classFieldDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function insertFieldBefore($fieldName, $jsonFieldDefinition)
    {
        $this->findField($fieldName, function (&$foundField, $index, &$parent) use ($jsonFieldDefinition) {
            if ($index === 0) {
                $index = 1;
            }

            $childs = $parent['childs'];

            array_splice($childs, $index, 0, [$jsonFieldDefinition]);

            $parent['childs'] = $childs;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function insertFieldAfter($fieldName, $jsonFieldDefinition)
    {
        $this->findField($fieldName, function (&$foundField, $index, &$parent) use ($jsonFieldDefinition) {
            $childs = $parent['childs'];

            array_splice($childs, $index + 1, 0, [$jsonFieldDefinition]);

            $parent['childs'] = $childs;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function replaceField($fieldName, $jsonFieldDefinition)
    {
        $this->findField($fieldName, function (&$foundField, $index, &$parent) use ($jsonFieldDefinition) {
            $foundField = $jsonFieldDefinition;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFieldProperties($fieldName, array $keyValues)
    {
        $this->findField($fieldName, function (&$foundField, $index, &$parent) use ($keyValues) {
            foreach ($keyValues as $key => $value) {
                $foundField[$key] = $value;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function removeField($fieldName)
    {
        $this->findField($fieldName, function (&$foundField, $index, &$parent) {
            unset($parent['childs'][$index]);
        });
    }

    /**
     * @param string $fieldName
     * @param \Closure $callback
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    protected function findField(string $fieldName, \Closure $callback)
    {
        $found = false;

        $traverseFunction = function ($children) use (&$traverseFunction, $fieldName, $callback, &$found) {
            foreach ($children['childs'] as $index => &$child) {
                if ($child['name'] === $fieldName) {
                    $callback($child, $index, $children);
                    $found = true;
                    break;
                } else {
                    if (array_key_exists('childs', $child)) {
                        $child = $traverseFunction($child);
                    }
                }
            }

            return $children;
        };

        $this->jsonDefinition['layoutDefinitions'] = $traverseFunction($this->jsonDefinition['layoutDefinitions']);

        if (!$found) {
            throw new ClassDefinitionFieldNotFoundException(sprintf('Field with name %s not found', $fieldName));
        }
    }
}