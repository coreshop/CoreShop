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

namespace CoreShop\Bundle\CoreBundle\Grid\Column\Resolver;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\ColumnResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\CoreElementColumnResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Schema\Column;
use Pimcore\Bundle\StudioBackendBundle\Grid\Schema\ColumnData;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\ElementTypes;
use Pimcore\Model\Element\ElementInterface;

final class StorePriceResolver implements ColumnResolverInterface, CoreElementColumnResolverInterface
{
    public function __construct(
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly MoneyFormatterInterface $moneyFormatter,
        private readonly LocaleContextInterface $localeContext,
    ) {
    }

    public function resolveForCoreElement(Column $column, ElementInterface $element): ColumnData
    {
        if (!$element instanceof ProductInterface) {
            return new ColumnData(
                key: $column->getKey(),
                locale: $column->getLocale(),
                value: null,
                fieldType: 'coreshop.storePrice',
            );
        }

        $config = $column->getConfig();
        $storeId = (int) ($config['storeId'] ?? 0);
        $store = $this->storeRepository->find($storeId);

        if (!$store instanceof StoreInterface) {
            return new ColumnData(
                key: $column->getKey(),
                locale: $column->getLocale(),
                value: null,
                fieldType: 'coreshop.storePrice',
            );
        }

        $price = $element->getStoreValuesOfType('price', $store) ?? 0;
        $currency = $store->getCurrency();
        $locale = $this->localeContext->getLocaleCode();

        $formatted = $this->moneyFormatter->format(
            (int) $price,
            $currency->getIsoCode(),
            $locale,
        );

        return new ColumnData(
            key: $column->getKey(),
            locale: $column->getLocale(),
            value: $formatted,
            fieldType: 'coreshop.storePrice',
        );
    }

    public function getType(): string
    {
        return 'coreshop.storePrice';
    }

    public function supportedElementTypes(): array
    {
        return [
            ElementTypes::TYPE_OBJECT,
        ];
    }
}
