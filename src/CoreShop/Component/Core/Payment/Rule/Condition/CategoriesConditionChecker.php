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
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\CategoriesConditionCheckerTrait;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

final class CategoriesConditionChecker extends AbstractConditionChecker
{
    use CategoriesConditionCheckerTrait {
        CategoriesConditionCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
    ) {
        $this->__traitConstruct($categoryRepository);
    }

    public function isPaymentProviderRuleValid(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
    ): bool {
        if (!$payable instanceof OrderInterface) {
            return false;
        }

        $cartItems = $payable->getItems() ?? [];

        $categoryIdsToCheck = $this->getCategoriesToCheck(
            $configuration['categories'],
            $payable->getStore(),
            $configuration['recursive'] ?: false,
        );

        foreach ($cartItems as $item) {
            $product = $item->getProduct();
            if ($product instanceof ProductInterface) {
                $categories = $product->getCategories();

                if (null === $categories) {
                    continue;
                }

                foreach ($categories as $category) {
                    if ($category instanceof ResourceInterface) {
                        if (in_array($category->getId(), $categoryIdsToCheck)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
