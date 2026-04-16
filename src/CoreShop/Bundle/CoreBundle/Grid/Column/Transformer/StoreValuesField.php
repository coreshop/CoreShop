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

namespace CoreShop\Bundle\CoreBundle\Grid\Column\Transformer;

use Pimcore\Bundle\StudioBackendBundle\Exception\Api\TransformerException;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\TransformerInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Util\AdvancedValue;

/**
 * Extracts a value (default "price") from a normalized coreShopStoreValues source field for a specific store.
 *
 * Input: array<storeId, ['name'=>string, 'currencySymbol'=>string, 'values'=>array, ...]>
 *        produced by StoreValuesAdapter::normalize().
 *
 * Output:
 *  - For "price" (and other monetary fields): integer cents (multiplied by decimal_factor),
 *    so the result can be chained with the coreshop_price_formatter transformer.
 *  - For other scalar fields: passed through unchanged.
 */
final class StoreValuesField implements TransformerInterface
{
    /**
     * Fields stored as integer cents in the database but normalized to display floats by StoreValuesAdapter.
     * For these we reverse the conversion so chaining with coreshop_price_formatter works.
     */
    private const PRICE_FIELDS = ['price'];

    public function __construct(
        private readonly int $decimalFactor,
    ) {
    }

    /**
     * @throws TransformerException
     */
    public function transform(array $value, array $config): array
    {
        $storeId = $this->normaliseInt($config['storeId'] ?? null);
        if ($storeId === null) {
            throw new TransformerException(
                $this->getName(),
                sprintf('Config "storeId" is required for the %s transformer.', $this->getKey()),
            );
        }

        $field = $config['field'] ?? 'price';
        if (!is_string($field) || $field === '') {
            throw new TransformerException(
                $this->getName(),
                sprintf('Config "field" must be a non-empty string for the %s transformer.', $this->getKey()),
            );
        }

        $results = [];
        foreach ($value as $val) {
            $results[] = $this->extract($val, $storeId, $field);
        }

        return $results;
    }

    /**
     * @throws TransformerException
     */
    private function extract(AdvancedValue $val, int $storeId, string $field): AdvancedValue
    {
        $data = $val->getValue();
        if (!is_array($data)) {
            throw new TransformerException(
                $this->getName(),
                sprintf(
                    'Source field "%s" did not produce a store-values array (got %s). '
                    . 'This transformer must be applied to a coreShopStoreValues field.',
                    $val->getFieldName(),
                    get_debug_type($data),
                ),
            );
        }

        $storeBlock = $data[$storeId] ?? null;
        if (!is_array($storeBlock)) {
            // No values configured for this store on this element — emit empty.
            return new AdvancedValue('string', null, $val->getFieldName());
        }

        $values = $storeBlock['values'] ?? null;
        if (!is_array($values) || !array_key_exists($field, $values)) {
            return new AdvancedValue('string', null, $val->getFieldName());
        }

        $extracted = $values[$field];

        // Reverse the display-value conversion for monetary fields, so that chaining
        // with coreshop_price_formatter (which expects integer cents) works.
        if (in_array($field, self::PRICE_FIELDS, true) && (is_int($extracted) || is_float($extracted))) {
            return new AdvancedValue(
                'integer',
                (int) round((float) $extracted * $this->decimalFactor),
                $val->getFieldName(),
            );
        }

        return new AdvancedValue($this->guessType($extracted), $extracted, $val->getFieldName());
    }

    private function guessType(mixed $extracted): string
    {
        return match (true) {
            is_int($extracted) => 'integer',
            is_float($extracted) => 'float',
            is_bool($extracted) => 'boolean',
            default => 'string',
        };
    }

    private function normaliseInt(mixed $raw): ?int
    {
        if (is_int($raw)) {
            return $raw > 0 ? $raw : null;
        }

        if (is_string($raw) && ctype_digit($raw)) {
            $int = (int) $raw;

            return $int > 0 ? $int : null;
        }

        return null;
    }

    public function getName(): string
    {
        return 'CoreShop Store Values Field';
    }

    public function getKey(): string
    {
        return 'coreshop_store_values_field';
    }

    public function getDescription(): string
    {
        return 'Extracts a value (default "price") from a coreShopStoreValues source field for a specific store. '
            . 'Monetary fields ("price") are returned as integer cents — chain with "CoreShop Price Formatter" to format them as currency.';
    }

    public function getConfigOptions(): array
    {
        return [
            'storeId' => [
                'type' => 'input',
                'label' => 'Store ID',
                'description' => 'The CoreShop store whose values to extract.',
                'required' => true,
            ],
            'field' => [
                'type' => 'input',
                'label' => 'Field',
                'description' => 'Property of the store-values block to extract (e.g. "price").',
                'default' => 'price',
            ],
        ];
    }
}
