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

use CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface ClassUpdateInterface
{
    /**
     * @param string $className
     */
    public function __construct($className);

    /**
     * Save Field Definition
     *
     * @return bool
     */
    public function save();

    /**
     * get a property from the class
     *
     * @param string $property
     * @return mixed
     */
    public function getProperty($property);

    /**
     * set a property for the class
     *
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public function setProperty($property, $value);

    /**
     * Check if Class has field
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasField($fieldName);

    /**
     * @param string $fieldName
     * @return Data|null
     */
    public function getFieldDefinition($fieldName);

    /**
     * Insert Field at the end
     *
     * @param array $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function insertField($jsonFieldDefinition);

    /**
     * Insert Field before another field
     *
     * @param string $fieldName
     * @param array $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function insertFieldBefore($fieldName, $jsonFieldDefinition);

    /**
     * Insert Field after another field
     *
     * @param string $fieldName
     * @param array $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function insertFieldAfter($fieldName, $jsonFieldDefinition);

    /**
     * Replace existing Field with a new Definition
     *
     * @param string $fieldName
     * @param array $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function replaceField($fieldName, $jsonFieldDefinition);

    /**
     * Replace Properties from any field
     *
     * @param string $fieldName
     * @param array $keyValues
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function replaceFieldProperties($fieldName, array $keyValues);

    /**
     * Remove existing Field
     *
     * @param string $fieldName
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function removeField($fieldName);
}