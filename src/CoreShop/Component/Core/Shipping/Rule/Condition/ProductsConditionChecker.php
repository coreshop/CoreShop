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

namespace CoreShop\Component\Core\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\ProductVariantsCheckerTrait;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use CoreShop\Component\Shipping\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class ProductsConditionChecker extends AbstractConditionChecker
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
    public function isShippingRuleValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address, array $configuration)
    {
        if (!$shippable instanceof StoreAwareInterface) {
            return false;
        }

        $productIdsToCheck = $this->getProductsToCheck($configuration['products'], $shippable->getStore(), $configuration['include_variants'] ?: false);

        $cartItems = $shippable->getItems();

        foreach ($cartItems as $item) {
            if ($item instanceof CartItemInterface && $item->getIsGiftItem()) {
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
