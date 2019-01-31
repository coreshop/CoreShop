<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Cart\Rule\Condition;

use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\ProductVariantsCheckerTrait;
use CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Product\Model\ProductInterface;

final class ProductsConditionChecker extends AbstractConditionChecker
{
    use ProductVariantsCheckerTrait {
        ProductVariantsCheckerTrait::__construct as private __traitConstruct;
    }

    /**
     * @param ProductVariantRepositoryInterface $productRepository
     */
    public function __construct(ProductVariantRepositoryInterface $productRepository)
    {
        $this->__traitConstruct($productRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, ?CartPriceRuleVoucherCodeInterface $voucher, array $configuration)
    {
        $productIdsToCheck = $this->getProductsToCheck($configuration['products'], $cart->getStore(), $configuration['include_variants'] ?: false);

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if (in_array($product->getId(), $productIdsToCheck)) {
                    return true;
                }
            }
        }

        return false;
    }
}
