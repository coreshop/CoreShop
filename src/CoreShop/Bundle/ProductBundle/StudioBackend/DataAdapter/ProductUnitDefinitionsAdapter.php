<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ProductBundle\StudioBackend\DataAdapter;

use CoreShop\Bundle\ProductBundle\CoreExtension\ProductUnitDefinitions;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\ArrayTransformerInterface;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\DataNormalizerInterface;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\Model\FieldContextData;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\SetterDataInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\UserInterface;

final readonly class ProductUnitDefinitionsAdapter implements SetterDataInterface, DataNormalizerInterface
{
    public function __construct(
        private ArrayTransformerInterface $serializer,
    ) {
    }

    public function normalize(
        mixed $value,
        Data $fieldDefinition,
    ): ?array {
        if (!$value instanceof ProductUnitDefinitionsInterface) {
            return null;
        }

        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $context->setGroups(['Default', 'Detailed']);

        return $this->serializer->toArray($value, $context);
    }

    public function getDataForSetter(
        Concrete $element,
        Data $fieldDefinition,
        string $key,
        array $data,
        UserInterface $user,
        ?FieldContextData $contextData = null,
        bool $isPatch = false,
    ): mixed {
        if (!$fieldDefinition instanceof ProductUnitDefinitions) {
            return null;
        }

        if (!isset($data[$key]) || !is_array($data[$key])) {
            return null;
        }

        $formData = $this->transformDataForForm($data[$key]);

        return $fieldDefinition->getDataFromEditmode($formData, $element);
    }

    /**
     * Transform data from frontend format to the format expected by the Symfony form.
     *
     * Frontend sends:
     * - unit as object with id, name, translations, etc.
     * - unitDefinitions array for additional units
     * - _tempKey for unsaved entries
     *
     * Form expects:
     * - unit as integer ID
     * - additionalUnitDefinitions key name
     * - No _tempKey fields
     */
    private function transformDataForForm(array $data): array
    {
        $result = [];

        // Preserve the ID if it exists
        if (isset($data['id'])) {
            $result['id'] = $data['id'];
        }

        // Transform defaultUnitDefinition (only if it has a valid unit)
        if (isset($data['defaultUnitDefinition']) && is_array($data['defaultUnitDefinition'])) {
            $defaultUnitId = $this->extractUnitId($data['defaultUnitDefinition']['unit'] ?? null);
            if ($defaultUnitId !== null) {
                $result['defaultUnitDefinition'] = $this->transformUnitDefinition($data['defaultUnitDefinition']);
            }
        }

        // Transform unitDefinitions to additionalUnitDefinitions
        if (isset($data['unitDefinitions']) && is_array($data['unitDefinitions'])) {
            $additionalUnitDefinitions = [];
            $defaultUnitId = isset($data['defaultUnitDefinition']) ? $this->extractUnitId($data['defaultUnitDefinition']['unit'] ?? null) : null;

            foreach ($data['unitDefinitions'] as $unitDefinition) {
                if (!is_array($unitDefinition)) {
                    continue;
                }

                $currentUnitId = $this->extractUnitId($unitDefinition['unit'] ?? null);

                // Skip entries without a unit (incomplete new entries from frontend)
                if ($currentUnitId === null) {
                    continue;
                }

                // Skip entries that match the default unit (they're not additional)
                if ($currentUnitId === $defaultUnitId) {
                    continue;
                }

                $additionalUnitDefinitions[] = $this->transformUnitDefinition($unitDefinition);
            }
            $result['additionalUnitDefinitions'] = $additionalUnitDefinitions;
        }

        return $result;
    }

    /**
     * Transform a single unit definition, extracting unit ID from object if needed.
     */
    private function transformUnitDefinition(array $definition): array
    {
        $result = [];

        // Extract unit ID from object or use directly if already an ID
        if (isset($definition['unit'])) {
            $result['unit'] = $this->extractUnitId($definition['unit']);
        }

        // Copy other fields
        if (isset($definition['conversionRate'])) {
            $result['conversionRate'] = $definition['conversionRate'];
        }

        if (isset($definition['precision'])) {
            $result['precision'] = $definition['precision'];
        }

        // Do NOT include _tempKey - it's only for frontend state management

        return $result;
    }

    /**
     * Extract the unit ID from various formats (object with id, direct integer, or numeric string).
     */
    private function extractUnitId(mixed $unit): ?int
    {
        if ($unit === null) {
            return null;
        }

        // Direct integer
        if (is_int($unit)) {
            return $unit;
        }

        // Numeric string (e.g., "2" from JSON)
        if (is_string($unit) && is_numeric($unit)) {
            return (int) $unit;
        }

        // Object/array with id property
        if (is_array($unit) && isset($unit['id'])) {
            return (int) $unit['id'];
        }

        return null;
    }
}
