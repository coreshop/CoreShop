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

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculator;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class ProductExtractor implements TrackingExtractorInterface
{
    /**
     * @var TaxedProductPriceCalculator
     */
    private $taxedPurchasablePriceCalculator;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param TaxedProductPriceCalculator $taxedPurchasablePriceCalculator
     * @param CurrencyContextInterface    $currencyContext
     */
    public function __construct(
        TaxedProductPriceCalculator $taxedPurchasablePriceCalculator,
        CurrencyContextInterface $currencyContext
    ) {
        $this->taxedPurchasablePriceCalculator = $taxedPurchasablePriceCalculator;
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof PurchasableInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($object, $data = []): array
    {
        $categories = [];

        if ($object instanceof ProductInterface) {
            $categories = $object->getCategories();
        }

        /**
         * @var $object PurchasableInterface
         */
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'category' => count($categories) > 0 ? $categories[0]->getName() : '',
            'sku' => $object instanceof ProductInterface ? $object->getSku() : '',
            'price' => $this->taxedPurchasablePriceCalculator->getPrice($object) / 100,
            'currency' => $this->currencyContext->getCurrency()->getIsoCode(),
            'categories' => array_map(function(CategoryInterface $category) {
                return [
                    'id' => $category->getId(),
                    'name' => $category->getName()
                ];
            }, $categories)
        ];
    }
}