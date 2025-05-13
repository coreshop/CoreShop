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

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\ProductVariantsCheckerTrait;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

class ProductsConditionChecker implements ConditionCheckerInterface
{
    use ProductVariantsCheckerTrait {
        ProductVariantsCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(
        ProductVariantRepositoryInterface $productRepository,
    ) {
        $this->__traitConstruct($productRepository);
    }

    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = [],
    ): bool {
        Assert::isInstanceOf($subject, ProductInterface::class);

        if (!array_key_exists('store', $params) || !$params['store'] instanceof StoreInterface) {
            return false;
        }

        $productIdsToCheck = $this->getProductsToCheck(
            $configuration['products'],
            $params['store'],
            $configuration['include_variants'] ?: false,
            [sprintf('cs_rule_variant_%s', $rule->getId())],
        );

        return in_array($subject->getId(), $productIdsToCheck);
    }
}
