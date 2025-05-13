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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class ProductExtractor implements TrackingExtractorInterface
{
    public function __construct(
        private TaxedProductPriceCalculatorInterface $taxedPurchasablePriceCalculator,
        private ShopperContextInterface $shopperContext,
        private int $decimalFactor,
    ) {
    }

    public function supports($object): bool
    {
        return $object instanceof PurchasableInterface;
    }

    public function updateMetadata($object, $data = []): array
    {
        $categories = [];

        if ($object instanceof ProductInterface) {
            $categories = $object->getCategories();
        }

        /**
         * @var PurchasableInterface $object
         */
        return array_merge($data, [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'category' => (is_array($categories) && count($categories) > 0) ? $categories[0]->getName() : '',
            'sku' => $object instanceof ProductInterface ? $object->getSku() : '',
            'price' => $this->taxedPurchasablePriceCalculator->getPrice(
                $object,
                $this->shopperContext->getContext(),
            ) / $this->decimalFactor,
            'currency' => $this->shopperContext->getCurrency()->getIsoCode(),
            'categories' => array_map(static function (CategoryInterface $category) {
                return [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                ];
            }, is_array($categories) ? $categories : []),
        ]);
    }
}
