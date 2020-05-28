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

use CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface ClassUpdateInterface
{
    /**
     * @param string $className
     */
    public function __construct(string $className);

    /**
     * Save Field Definition.
     *
     * @return bool
     */
    public function save(): bool;

    /**
     * get a property from the class.
     *
     * @param string $property
     *
     * @return array
     */
    public function getProperty($property): array;

    /**
     * set a property for the class.
     *
     * @param string $property
     * @param mixed  $value
     */
    public function setProperty($property, $value): void;

    /**
     * Check if Class has field.
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField($fieldName): bool;

    /**
     * @param string $fieldName
     *
     * @return Data|null
     */
    public function getFieldDefinition($fieldName): ?Data;

    /**
     * Insert Field at the end.
     *
     * @param array $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function insertField($jsonFieldDefinition): void;

    /**
     * Insert Field before another field.
     *
     * @param string $fieldName
     * @param array  $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function insertFieldBefore($fieldName, $jsonFieldDefinition): void;

    /**
     * Insert Field after another field.
     *
     * @param string $fieldName
     * @param array  $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function insertFieldAfter($fieldName, $jsonFieldDefinition): void;

    /**
     * Replace existing Field with a new Definition.
     *
     * @param string $fieldName
     * @param array  $jsonFieldDefinition
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function replaceField($fieldName, $jsonFieldDefinition): void;

    /**
     * Replace Properties from any field.
     *
     * @param string $fieldName
     * @param array  $keyValues
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function replaceFieldProperties($fieldName, array $keyValues): void;

    /**
     * Remove existing Field.
     *
     * @param string $fieldName
     *
     * @throws ClassDefinitionFieldNotFoundException
     */
    public function removeField($fieldName): void;
}
