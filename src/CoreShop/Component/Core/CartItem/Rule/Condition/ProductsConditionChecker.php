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

    public function __construct(
        ProductVariantRepositoryInterface $productRepository,
    ) {
        $this->__traitConstruct($productRepository);
    }

    public function isValidForOrderItem(
        OrderItemInterface $orderItem,
        CartPriceRuleInterface $cartPriceRule,
        ?CartPriceRuleVoucherCodeInterface $voucher,
        array $configuration,
    ): bool {
        $productIdsToCheck = $this->getProductsToCheck(
            $configuration['products'],
            $orderItem->getOrder()->getStore(),
            $configuration['include_variants'] ?: false,
            [sprintf('cs_rule_variant_%s', $cartPriceRule->getId())],
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
