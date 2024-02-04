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

use CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException;
use Pimcore\Bundle\CoreBundle\Migrations\Version20211117173000;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class AbstractDefinitionUpdate implements ClassUpdateInterface
{
    protected array $jsonDefinition;

    protected array $originalJsonDefinition;

    protected array $fieldDefinitions;

    protected string $childrenPath = 'childs';

    public function __construct(
        ) {
        if (class_exists(Version20211117173000::class)) {
            $this->childrenPath = 'children';
        }
    }

    abstract public function save(): bool;

    public function getJsonDefinition(): array
    {
        return $this->jsonDefinition;
    }

    public function getOriginalJsonDefinition(): array
    {
        return $this->originalJsonDefinition;
    }

    public function getProperty(string $property): mixed
    {
        return $this->jsonDefinition[$property];
    }

    public function setProperty(string $property, $value): void
    {
        $this->jsonDefinition[$property] = $value;
    }

    public function hasField(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->fieldDefinitions);
    }

    public function getFieldDefinition(string $fieldName): ?Data
    {
        if (!$this->hasField($fieldName)) {
            throw new \InvalidArgumentException(sprintf('Field with Name %s not found', $fieldName));
        }

        return $this->fieldDefinitions[$fieldName];
    }

    public function removeField(string $fieldName): void
    {
        $this->findField(
            $fieldName,
            false,
            function (array &$foundField, int $index, array &$parent) {
                unset($parent[$this->childrenPath][$index]);
            },
        );
    }

    public function insertField(array $jsonFieldDefinition): void
    {
        $this->jsonDefinition['layoutDefinitions'][$this->childrenPath][0][$this->childrenPath][] = $jsonFieldDefinition;
    }

    public function insertFieldBefore(string $fieldName, array $jsonFieldDefinition): void
    {
        $this->findField(
            $fieldName,
            false,
            function (array &$foundField, int $index, array &$parent) use ($jsonFieldDefinition) {
                if ($index === 0) {
                    $index = 1;
                }

                $childs = $parent[$this->childrenPath];

                array_splice($childs, $index, 0, [$jsonFieldDefinition]);

                $parent[$this->childrenPath] = $childs;
            },
        );
    }

    public function insertFieldAfter(string $fieldName, array $jsonFieldDefinition): void
    {
        $this->findField(
            $fieldName,
            false,
            function (array &$foundField, int $index, array &$parent) use ($jsonFieldDefinition) {
                $childs = $parent[$this->childrenPath];

                array_splice($childs, $index + 1, 0, [$jsonFieldDefinition]);

                $parent[$this->childrenPath] = $childs;
            },
        );
    }

    public function replaceFieldProperties(string $fieldName, array $keyValues): void
    {
        $this->findField(
            $fieldName,
            false,
            function (array &$foundField, int $index, array &$parent) use ($keyValues) {
                foreach ($keyValues as $key => $value) {
                    $foundField[$key] = $value;
                }
            },
        );
    }

    public function replaceField(string $fieldName, array $jsonFieldDefinition): void
    {
        $this->findField(
            $fieldName,
            false,
            function (array &$foundField, int $index, array &$parent) use ($jsonFieldDefinition) {
                $foundField = $jsonFieldDefinition;
            },
        );
    }

    public function insertLayoutBefore(string $fieldName, array $jsonFieldDefinition): void
    {
        $this->findField(
            $fieldName,
            true,
            function (array &$foundField, int $index, array &$parent) use ($jsonFieldDefinition) {
                if ($index === 0) {
                    $index = 1;
                }

                $childs = $parent[$this->childrenPath];

                array_splice($childs, $index, 0, [$jsonFieldDefinition]);

                $parent[$this->childrenPath] = $childs;
            },
        );
    }

    public function insertLayoutAfter(string $fieldName, array $jsonFieldDefinition): void
    {
        $this->findField(
            $fieldName,
            true,
            function (array &$foundField, int $index, array &$parent) use ($jsonFieldDefinition) {
                $childs = $parent[$this->childrenPath];

                array_splice($childs, $index + 1, 0, [$jsonFieldDefinition]);

                $parent[$this->childrenPath] = $childs;
            },
        );
    }

    public function replaceLayout(string $fieldName, array $jsonFieldDefinition): void
    {
        $this->findField(
            $fieldName,
            true,
            function (array &$foundField, int $index, array &$parent) use ($jsonFieldDefinition) {
                $foundField = $jsonFieldDefinition;
            },
        );
    }

    public function replaceLayoutProperties(string $fieldName, array $keyValues): void
    {
        $this->findField(
            $fieldName,
            true,
            function (array &$foundField, int $index, array &$parent) use ($keyValues) {
                foreach ($keyValues as $key => $value) {
                    $foundField[$key] = $value;
                }
            },
        );
    }

    public function removeLayout(string $fieldName): void
    {
        $this->findField(
            $fieldName,
            true,
            function (array &$foundField, int $index, array &$parent) {
                unset($parent[$this->childrenPath][$index]);
            },
        );
    }

    protected function findField(string $fieldName, bool $layoutElement, callable $callback): void
    {
        $found = false;

        $traverseFunction = function (array $children) use (&$traverseFunction, $layoutElement, $fieldName, $callback, &$found): array {
            foreach ($children[$this->childrenPath] as $index => &$child) {
                $eligible = true;

                if ($layoutElement && !array_key_exists($this->childrenPath, $child)) {
                    $eligible = false;
                }

                if (!$layoutElement && array_key_exists($this->childrenPath, $child)) {
                    $eligible = false;
                }

                if ($eligible && $child['name'] === $fieldName) {
                    $callback($child, $index, $children);
                    $found = true;

                    break;
                }

                if (array_key_exists($this->childrenPath, $child)) {
                    $child = $traverseFunction($child);
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
