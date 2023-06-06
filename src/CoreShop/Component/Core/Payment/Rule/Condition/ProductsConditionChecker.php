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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Payment\Rule\Condition;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\ProductVariantsCheckerTrait;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class ProductsConditionChecker extends AbstractConditionChecker
{
    use ProductVariantsCheckerTrait {
        ProductVariantsCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(
        ProductVariantRepositoryInterface $productRepository,
    ) {
        $this->__traitConstruct($productRepository);
    }

    public function isPaymentProviderRuleValid(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
    ): bool {
        if (!$payable instanceof StoreAwareInterface) {
            return false;
        }

        $productIdsToCheck = $this->getProductsToCheck(
            $configuration['products'],
            $payable->getStore(),
            $configuration['include_variants'] ?: false,
        );

        if (!$payable instanceof OrderInterface) {
            return false;
        }
        $cartItems = $payable->getItems();

        foreach ($cartItems as $item) {
            if ($item instanceof OrderItemInterface && $item->getIsGiftItem()) {
                continue;
            }

            if ($item->getProduct() instanceof ProductInterface) {
                if (in_array($item->getProduct()->getId(), $productIdsToCheck)) {
                    return true;
                }
            }
        }

        return false;
    }
}
