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

namespace CoreShop\Component\Core\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\ProductVariantsCheckerTrait;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class ProductsConditionChecker extends AbstractConditionChecker
{
    public const SHIPPING_RULE_RECURSIVE_VARIANT_CACHE_TAG = 'cs_shipping_rule_recursive_variant';

    use ProductVariantsCheckerTrait {
        ProductVariantsCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(
        ProductVariantRepositoryInterface $productRepository,
    ) {
        $this->__traitConstruct($productRepository);
    }

    public function isShippingRuleValid(
        CarrierInterface $carrier,
        ShippableInterface $shippable,
        AddressInterface $address,
        array $configuration,
    ): bool {
        if (!$shippable instanceof StoreAwareInterface) {
            return false;
        }

        $productIdsToCheck = $this->getProductsToCheck(
            $configuration['products'],
            $shippable->getStore(),
            $configuration['include_variants'] ?: false,
            [self::SHIPPING_RULE_RECURSIVE_VARIANT_CACHE_TAG],
        );

        $cartItems = $shippable->getItems();

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
