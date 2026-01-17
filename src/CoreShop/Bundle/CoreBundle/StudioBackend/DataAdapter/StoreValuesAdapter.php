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

namespace CoreShop\Bundle\CoreBundle\StudioBackend\DataAdapter;

use CoreShop\Bundle\CoreBundle\CoreExtension\StoreValues;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\DataNormalizerInterface;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\Model\FieldContextData;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\SetterDataInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\UserInterface;

final readonly class StoreValuesAdapter implements SetterDataInterface, DataNormalizerInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private StoreRepositoryInterface $storeRepository,
        private int $decimalFactor,
    ) {
    }

    /**
     * Convert integer price to display value (e.g., 38200 -> 382.00)
     */
    private function toDisplayPrice(int|float|null $integerPrice): float
    {
        if ($integerPrice === null) {
            return 0.0;
        }

        return (float) $integerPrice / $this->decimalFactor;
    }

    /**
     * Convert all prices in the values array from integer to display format.
     * This ensures the frontend always works with display values.
     */
    private function convertPricesToDisplayValues(array $values): array
    {
        // Convert main price
        if (isset($values['price'])) {
            $values['price'] = $this->toDisplayPrice($values['price']);
        }

        // Convert unit definition prices
        if (isset($values['productUnitDefinitionPrices']) && is_array($values['productUnitDefinitionPrices'])) {
            foreach ($values['productUnitDefinitionPrices'] as $key => $priceData) {
                if (isset($priceData['price'])) {
                    $values['productUnitDefinitionPrices'][$key]['price'] = $this->toDisplayPrice($priceData['price']);
                }
            }
        }

        return $values;
    }

    public function normalize(
        mixed $value,
        Data $fieldDefinition,
    ): ?array {
        $storeData = [];
        $stores = $this->storeRepository->findAll();

        if (!is_array($value)) {
            // Return empty structure for all stores
            foreach ($stores as $store) {
                $currency = $store->getCurrency();
                $storeData[$store->getId()] = [
                    'name' => $store->getName(),
                    'currencySymbol' => $currency?->getSymbol() ?? '',
                    'values' => ['price' => 0],
                    'inherited' => false,
                    'inheritable' => false,
                ];
            }

            return $storeData;
        }

        foreach ($value as $storeValuesEntity) {
            if (!$storeValuesEntity instanceof ProductStoreValuesInterface) {
                continue;
            }

            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);
            $values = $this->serializer->toArray($storeValuesEntity, $context);

            // Convert integer prices to display values for frontend
            $values = $this->convertPricesToDisplayValues($values);

            $store = $storeValuesEntity->getStore();
            $currency = $store->getCurrency();

            $storeData[$store->getId()] = [
                'name' => $store->getName(),
                'currencySymbol' => $currency?->getSymbol(),
                'values' => $values,
                'inherited' => false,
                'inheritable' => false,
            ];
        }

        // Fill missing stores with empty values
        foreach ($stores as $store) {
            if (array_key_exists($store->getId(), $storeData)) {
                continue;
            }

            $currency = $store->getCurrency();
            $storeData[$store->getId()] = [
                'name' => $store->getName(),
                'currencySymbol' => $currency?->getSymbol() ?? '',
                'values' => ['price' => 0],
                'inherited' => false,
                'inheritable' => false,
            ];
        }

        return $storeData;
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
        if (!$fieldDefinition instanceof StoreValues) {
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
     * - Nested 'values' object with price and productUnitDefinitionPrices
     * - productUnitDefinitionPrices.unitDefinition as object with id
     *
     * Form expects:
     * - Flat structure with price, productUnitDefinitionPrices at top level
     * - productUnitDefinitionPrices.unitDefinition as integer ID
     */
    private function transformDataForForm(array $data): array
    {
        $result = [];

        foreach ($data as $storeId => $storeData) {
            if (!is_array($storeData)) {
                continue;
            }

            $values = $storeData['values'] ?? [];
            $transformedStoreData = [];

            // Preserve the entity ID if it exists
            if (isset($values['id'])) {
                $transformedStoreData['id'] = $values['id'];
            }

            // Copy price
            if (isset($values['price'])) {
                $transformedStoreData['price'] = $values['price'];
            }

            // Copy taxRule if exists
            if (isset($values['taxRule'])) {
                $transformedStoreData['taxRule'] = is_array($values['taxRule'])
                    ? ($values['taxRule']['id'] ?? null)
                    : $values['taxRule'];
            }

            // Transform productUnitDefinitionPrices
            if (isset($values['productUnitDefinitionPrices']) && is_array($values['productUnitDefinitionPrices'])) {
                $transformedStoreData['productUnitDefinitionPrices'] = array_map(
                    fn(array $priceData) => $this->transformUnitDefinitionPrice($priceData),
                    $values['productUnitDefinitionPrices']
                );
            }

            $result[$storeId] = $transformedStoreData;
        }

        return $result;
    }

    /**
     * Transform a single unit definition price entry.
     */
    private function transformUnitDefinitionPrice(array $priceData): array
    {
        $result = [];

        if (isset($priceData['price'])) {
            $result['price'] = $priceData['price'];
        }

        if (isset($priceData['unitDefinition'])) {
            // Extract unitDefinition ID from object or use directly if already an ID
            $unitDef = $priceData['unitDefinition'];
            if (is_array($unitDef) && isset($unitDef['id'])) {
                $result['unitDefinition'] = (int) $unitDef['id'];
            } elseif (is_numeric($unitDef)) {
                $result['unitDefinition'] = (int) $unitDef;
            }
        }

        return $result;
    }
}
