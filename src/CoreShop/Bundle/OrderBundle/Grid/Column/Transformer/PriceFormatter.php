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

namespace CoreShop\Bundle\OrderBundle\Grid\Column\Transformer;

use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\TransformerException;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\TransformerInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Util\AdvancedValue;

final class PriceFormatter implements TransformerInterface
{
    public function __construct(
        private readonly MoneyFormatterInterface $moneyFormatter,
        private readonly LocaleContextInterface $localeService,
    ) {
    }

    /**
     * @throws TransformerException
     */
    public function transform(array $value, array $config): array
    {
        $staticCurrency = $this->normaliseString($config['currencyIsoCode'] ?? null);
        $currencyField = $this->normaliseString($config['currencyField'] ?? null);

        if ($staticCurrency === null && $currencyField === null) {
            throw new TransformerException(
                $this->getName(),
                sprintf(
                    'Either "currencyIsoCode" or "currencyField" must be configured for the %s transformer.',
                    $this->getKey(),
                ),
            );
        }

        $currency = $currencyField !== null
            ? $this->resolveCurrencyFromField($value, $currencyField)
            : $staticCurrency;

        $locale = $this->localeService->getLocaleCode();
        $results = [];
        foreach ($value as $val) {
            // The field carrying the currency is consumed — drop it from the output.
            if ($currencyField !== null && $val->getFieldName() === $currencyField) {
                continue;
            }

            $results[] = $this->formatValue($val, $currency, $locale);
        }

        return $results;
    }

    /**
     * @param AdvancedValue[] $values
     *
     * @throws TransformerException
     */
    private function resolveCurrencyFromField(array $values, string $currencyField): string
    {
        foreach ($values as $val) {
            if ($val->getFieldName() !== $currencyField) {
                continue;
            }

            $raw = $val->getValue();
            if (is_string($raw) && $raw !== '') {
                return $raw;
            }

            throw new TransformerException(
                $this->getName(),
                sprintf(
                    'Currency source field "%s" did not produce a non-empty ISO code (got %s).',
                    $currencyField,
                    get_debug_type($raw),
                ),
            );
        }

        throw new TransformerException(
            $this->getName(),
            sprintf(
                'Currency source field "%s" is not present in the selected source fields. '
                . 'Add it to the source fields of this advanced column.',
                $currencyField,
            ),
        );
    }

    /**
     * @throws TransformerException
     */
    private function formatValue(AdvancedValue $val, string $currency, string $locale): AdvancedValue
    {
        $data = $val->getValue();
        if (!is_int($data)) {
            throw new TransformerException(
                $this->getName(),
                sprintf(
                    'Source field "%s" produced a non-integer value (got %s). '
                    . 'Price fields must be integer cents.',
                    $val->getFieldName(),
                    get_debug_type($data),
                ),
            );
        }

        return new AdvancedValue(
            'string',
            $this->moneyFormatter->format($data, $currency, $locale),
            $val->getFieldName(),
        );
    }

    private function normaliseString(mixed $raw): ?string
    {
        return is_string($raw) && $raw !== '' ? $raw : null;
    }

    public function getName(): string
    {
        return 'CoreShop Price Formatter';
    }

    public function getKey(): string
    {
        return 'coreshop_price_formatter';
    }

    public function getDescription(): string
    {
        return 'Formats integer prices (cents) as a localized currency string. '
            . 'Provide either a static ISO currency code or the name of a sibling source field that holds the currency code. '
            . 'When a sibling currency field is used, it is consumed and removed from the output.';
    }

    public function getConfigOptions(): array
    {
        return [
            'currencyIsoCode' => [
                'type' => 'input',
                'label' => 'Static Currency ISO Code',
                'description' => 'ISO 4217 currency code (e.g. "EUR"). Used when no currency field is set.',
                'default' => '',
            ],
            'currencyField' => [
                'type' => 'input',
                'label' => 'Currency Field Name',
                'description' => 'Name of a sibling source field (in the same advanced column) containing the currency ISO code. Overrides the static code.',
                'default' => '',
            ],
        ];
    }
}
