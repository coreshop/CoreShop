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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;
use Webmozart\Assert\Assert;

final class CartPriceRuleContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private CartPriceRuleRepositoryInterface $cartPriceRuleRepository)
    {
    }

    /**
     * @Transform /^cart rule "([^"]+)"$/
     */
    public function getCartPriceRuleByProductAndName($ruleName): CartPriceRuleInterface
    {
        $rule = $this->cartPriceRuleRepository->findOneBy(['name' => $ruleName]);

        Assert::isInstanceOf($rule, CartPriceRuleInterface::class);

        return $rule;
    }

    /**
     * @Transform /^(cart rule)$/
     */
    public function getLatestCartPriceRule(): CartPriceRuleInterface
    {
        $resource = $this->sharedStorage->getLatestResource();

        Assert::isInstanceOf($resource, CartPriceRuleInterface::class);

        return $resource;
    }
}
