<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductPriceHelper extends Helper implements ProductPriceHelperInterface
{
    /**
     * @var TaxedProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @param TaxedProductPriceCalculatorInterface $productPriceCalculator
     * @param ShopperContextInterface              $shopperContext
     */
    public function __construct(
        TaxedProductPriceCalculatorInterface $productPriceCalculator,
        ShopperContextInterface $shopperContext
    ) {
        $this->productPriceCalculator = $productPriceCalculator;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $product, $withTax = true, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getPrice without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        return $this->productPriceCalculator->getPrice($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $product, $withTax = true, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getDiscountPrice without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        return $this->productPriceCalculator->getDiscountPrice($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $product, $withTax = true, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getRetailPrice without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        return $this->productPriceCalculator->getRetailPrice($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $product, $withTax = true, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getDiscount without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        return $this->productPriceCalculator->getDiscount($product, $context, $withTax);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_price';
    }
}
