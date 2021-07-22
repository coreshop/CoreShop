<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Order\Generator\CartPriceRuleVoucherCodeGenerator;
use CoreShop\Component\Order\Generator\CodeGeneratorCheckerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherGenerator;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class CartPriceRuleVoucherCodeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $cartPriceRuleVoucherRepository;

    /**
     * @var FactoryInterface
     */
    private $cartPriceRuleVoucherCodeFactory;

    /**
     * @var CartPriceRuleVoucherCodeGenerator
     */
    private $cartPriceRuleGenerator;

    /**
     * @var CodeGeneratorCheckerInterface
     */
    private $cartPriceRuleGeneratorChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherRepository,
        FactoryInterface $cartPriceRuleVoucherCodeFactory,
        CartPriceRuleVoucherCodeGenerator $cartPriceRuleGenerator,
        CodeGeneratorCheckerInterface $cartPriceRuleGeneratorChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->cartPriceRuleVoucherRepository = $cartPriceRuleVoucherRepository;
        $this->cartPriceRuleVoucherCodeFactory = $cartPriceRuleVoucherCodeFactory;
        $this->cartPriceRuleGenerator = $cartPriceRuleGenerator;
        $this->cartPriceRuleGeneratorChecker = $cartPriceRuleGeneratorChecker;
    }

    /**
     * @Given /^I want to generate ([^"]+) codes with a length of ([^"]+) in ([^"]+) characters for (cart rule "[^"]+") with prefix "([^"]+)" and suffix "([^"]+)"$/
     * @Given /^I want to generate ([^"]+) codes with a length of ([^"]+) in ([^"]+) characters for the (cart rule) with prefix "([^"]+)" and suffix "([^"]+)"$/
     * @Given /^I want to generate ([^"]+) codes with a length of ([^"]+) in ([^"]+) characters for (cart rule "[^"]+")$/
     * @Given /^I want to generate ([^"]+) codes with a length of ([^"]+) in ([^"]+) characters for the (cart rule)$/
     */
    public function iWantToGenerateCodes(int $numberOfCodes, int $lenghtPerCode, string $chars, CartPriceRuleInterface $cartPriceRule, ?string $prefix = null, ?string $suffix = null)
    {
        $generator = new CartPriceRuleVoucherGenerator();
        $generator->setAmount($numberOfCodes);
        $generator->setLength($lenghtPerCode);
        $generator->setFormat($chars);
        $generator->setPrefix($prefix);
        $generator->setPrefix($suffix);
        $generator->setCartPriceRule($cartPriceRule);

        $possible = $this->cartPriceRuleGeneratorChecker->isGenerationPossible($generator);
        $amountPossible = $this->cartPriceRuleGeneratorChecker->getPossibleGenerationAmount($generator);

        $this->sharedStorage->set('code-generation-possible', $possible);
        $this->sharedStorage->set('code-generation-amount-possible', $amountPossible);
    }
}
