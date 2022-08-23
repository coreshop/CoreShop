<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\CartItem\Rule\Condition;

use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\ProductVariantsCheckerTrait;
use CoreShop\Component\Order\CartItem\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;

final class ProductsConditionChecker extends AbstractConditionChecker
{
    use ProductVariantsCheckerTrait {
        ProductVariantsCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(ProductVariantRepositoryInterface $productRepository)
    {
        $this->__traitConstruct($productRepository);
    }

    public function isValidForOrderItem(
        OrderItemInterface $orderItem,
        CartPriceRuleInterface $cartPriceRule,
        ?CartPriceRuleVoucherCodeInterface $voucher,
        array $configuration
    ): bool {
        $productIdsToCheck = $this->getProductsToCheck(
            $configuration['products'],
            $orderItem->getOrder()->getStore(),
            $configuration['include_variants'] ?: false
        );

        if ($orderItem->getIsGiftItem()) {
            return false;
        }

        $product = $orderItem->getProduct();

        if (!$product instanceof ProductInterface) {
            return false;
        }

        if (in_array($product->getId(), $productIdsToCheck)) {
            return true;
        }

        return false;
    }
}
