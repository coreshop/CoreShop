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

namespace CoreShop\Component\Core\Tracking\Extractor;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;

class ProductExtractor implements TrackingExtractorInterface
{
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $taxedPurchasablePriceCalculator;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var int
     */
    private $decimalFactor;

    /**
     * @param TaxedProductPriceCalculatorInterface $taxedPurchasablePriceCalculator
     * @param ShopperContextInterface              $shopperContext
     * @param int                                  $decimalFactor
     */
    public function __construct(
        TaxedProductPriceCalculatorInterface $taxedPurchasablePriceCalculator,
        ShopperContextInterface $shopperContext,
        int $decimalFactor
    ) {
        $this->taxedPurchasablePriceCalculator = $taxedPurchasablePriceCalculator;
        $this->shopperContext = $shopperContext;
        $this->decimalFactor = $decimalFactor;
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
        return array_merge($data, [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'category' => (is_array($categories) && count($categories) > 0) ? $categories[0]->getName() : '',
            'sku' => $object instanceof ProductInterface ? $object->getSku() : '',
            'price' => $this->taxedPurchasablePriceCalculator->getPrice($object, $this->shopperContext->getContext()) / $this->decimalFactor,
            'currency' => $this->shopperContext->getCurrency()->getIsoCode(),
            'categories' => array_map(function (CategoryInterface $category) {
                return [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                ];
            }, is_array($categories) ? $categories : []),
        ]);
    }
}
