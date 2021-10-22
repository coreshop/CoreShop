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

declare(strict_types=1);

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

    public function __construct(ProductVariantRepositoryInterface $productRepository)
    {
        $this->__traitConstruct($productRepository);
    }

    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = []
    ): bool {
        Assert::isInstanceOf($subject, ProductInterface::class);

        if (!array_key_exists('store', $params) || !$params['store'] instanceof StoreInterface) {
            return false;
        }

        $productIdsToCheck = $this->getProductsToCheck(
            $configuration['products'],
            $params['store'],
            $configuration['include_variants'] ?: false
        );

        return in_array($subject->getId(), $productIdsToCheck);
    }
}
