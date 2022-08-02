<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\ClassDefinition\Data;

interface ClassUpdateInterface
{
    public function save(): bool;

    public function getProperty(string $property): array;

    public function setProperty(string $property, $value): void;

    public function hasField(string $fieldName): bool;

    public function getFieldDefinition(string $fieldName): ?Data;

    public function insertField(array $jsonFieldDefinition): void;

    public function insertFieldBefore(string $fieldName, array $jsonFieldDefinition): void;

    public function insertFieldAfter(string $fieldName, array $jsonFieldDefinition): void;

    public function replaceField(string $fieldName, array $jsonFieldDefinition): void;

    public function replaceFieldProperties(string $fieldName, array $keyValues): void;

    public function removeField(string $fieldName): void;

    public function insertLayoutBefore(string $fieldName, array $jsonFieldDefinition): void;

    public function insertLayoutAfter(string $fieldName, array $jsonFieldDefinition): void;

    public function replaceLayout(string $fieldName, array $jsonFieldDefinition): void;

    public function replaceLayoutProperties(string $fieldName, array $keyValues): void;

    public function removeLayout(string $fieldName): void;
}
