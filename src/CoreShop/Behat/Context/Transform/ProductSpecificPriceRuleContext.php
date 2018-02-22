<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductSpecificPriceRuleContext implements Context
{
    /**
     * @var ProductSpecificPriceRuleRepositoryInterface
     */
    private $productSpecificPriceRuleRepository;

    /**
     * @param ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository
     */
    public function __construct(ProductSpecificPriceRuleRepositoryInterface $productSpecificPriceRuleRepository)
    {
        $this->productSpecificPriceRuleRepository = $productSpecificPriceRuleRepository;
    }

    /**
     * @Transform /^specific price rule "([^"]+)"$/
     */
    public function getPriceRuleByProductAndName($ruleName)
    {
        $rule = $this->productSpecificPriceRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, ProductSpecificPriceRuleInterface::class);

        return $rule;
    }
}
