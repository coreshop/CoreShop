<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException;

abstract class AbstractDefinitionUpdate implements ClassUpdateInterface
{
    /**
     * @var array
     */
    protected $jsonDefinition;

    /**
     * @var array
     */
    protected $fieldDefinitions;

    /**
     * {@inheritdoc}
     */
    abstract public function save();

    /**
     * {@inheritdoc}
     */
    public function getProperty($property)
    {
        return $this->jsonDefinition[$property];
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($property, $value)
    {
        $this->jsonDefinition[$property] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($fieldName)
    {
        return array_key_exists($fieldName, $this->fieldDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($fieldName)
    {
        if (!$this->hasField($fieldName)) {
            throw new \InvalidArgumentException(sprintf('Field with Name %s not found', $fieldName));
        }

        return $this->fieldDefinitions[$fieldName];
    }

    /**
     * {@inheritdoc}
     */
    public function insertField($jsonFieldDefinition)
    {
        $this->jsonDefinition['layoutDefinitions']['childs'][0]['childs'][] = $jsonFieldDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function insertFieldBefore($fieldName, $jsonFieldDefinition)
    {
        $this->findField(
            $fieldName,
            function (&$foundField, $index, &$parent) use ($jsonFieldDefinition) {
                if ($index === 0) {
                    $index = 1;
                }

                $childs = $parent['childs'];

                array_splice($childs, $index, 0, [$jsonFieldDefinition]);

                $parent['childs'] = $childs;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function insertFieldAfter($fieldName, $jsonFieldDefinition)
    {
        $this->findField(
            $fieldName,
            function (&$foundField, $index, &$parent) use ($jsonFieldDefinition) {
                $childs = $parent['childs'];

                array_splice($childs, $index + 1, 0, [$jsonFieldDefinition]);

                $parent['childs'] = $childs;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function replaceField($fieldName, $jsonFieldDefinition)
    {
        $this->findField(
            $fieldName,
            function (&$foundField, $index, &$parent) use ($jsonFieldDefinition) {
                $foundField = $jsonFieldDefinition;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFieldProperties($fieldName, array $keyValues)
    {
        $this->findField(
            $fieldName,
            function (&$foundField, $index, &$parent) use ($keyValues) {
                foreach ($keyValues as $key => $value) {
                    $foundField[$key] = $value;
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeField($fieldName)
    {
        $this->findField(
            $fieldName,
            function (&$foundField, $index, &$parent) {
                unset($parent['childs'][$index]);
            }
        );
    }

    /**
     * @param string   $fieldName
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
