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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Action\FreeShippingConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Action\GiftProductConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CategoriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CurrenciesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomerGroupsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ProductsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\StoresConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleActionType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleConditionType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\DiscountAmountConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\DiscountPercentConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\SurchargeAmountConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\SurchargePercentConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\AmountConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Webmozart\Assert\Assert;

final class CartPriceRuleContext implements Context
{
    use ConditionFormTrait;
    use ActionFormTrait;

    private SharedStorageInterface $sharedStorage;
    private ObjectManager $objectManager;
    private FormFactoryInterface $formFactory;
    private FormTypeRegistryInterface $conditionFormTypeRegistry;
    private FormTypeRegistryInterface $actionFormTypeRegistry;
    private FactoryInterface $cartPriceRuleFactory;
    private CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherRepository;
    private CartPriceRuleProcessorInterface $cartPriceRuleProcessor;
    private CartManagerInterface $cartManager;
    private FactoryInterface $cartPriceRuleVoucherCodeFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $conditionFormTypeRegistry,
        FormTypeRegistryInterface $actionFormTypeRegistry,
        FactoryInterface $cartPriceRuleFactory,
        CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherRepository,
        CartPriceRuleProcessorInterface $cartPriceRuleProcessor,
        CartManagerInterface $cartManager,
        FactoryInterface $cartPriceRuleVoucherCodeFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->formFactory = $formFactory;
        $this->conditionFormTypeRegistry = $conditionFormTypeRegistry;
        $this->actionFormTypeRegistry = $actionFormTypeRegistry;
        $this->cartPriceRuleFactory = $cartPriceRuleFactory;
        $this->cartPriceRuleVoucherRepository = $cartPriceRuleVoucherRepository;
        $this->cartPriceRuleProcessor = $cartPriceRuleProcessor;
        $this->cartManager = $cartManager;
        $this->cartPriceRuleVoucherCodeFactory = $cartPriceRuleVoucherCodeFactory;
    }

    /**
     * @Given /^I apply the voucher code "([^"]+)" to (my cart)$/
     */
    public function iApplyTheCartRuleToMyCart($voucherCode, OrderInterface $cart): void
    {
        $voucherCode = $this->cartPriceRuleVoucherRepository->findByCode($voucherCode);

        /**
         * @var $voucherCode   CartPriceRuleVoucherCodeInterface
         * @var $cartPriceRule CartPriceRuleInterface
         */
        Assert::isInstanceOf($voucherCode, CartPriceRuleVoucherCodeInterface::class);

        $cartPriceRule = $voucherCode->getCartPriceRule();

        Assert::isInstanceOf($cartPriceRule, CartPriceRuleInterface::class);
        Assert::true($this->cartPriceRuleProcessor->process($cart, $cartPriceRule, $voucherCode));

        $this->cartManager->persistCart($cart);
    }

    /**
     * @Given /^adding a cart price rule named "([^"]+)"$/
     */
    public function addingACartPriceRule($ruleName): void
    {
        /**
         * @var CartPriceRuleInterface $rule
         */
        $rule = $this->cartPriceRuleFactory->createNew();
        $rule->setName($ruleName);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();

        $this->sharedStorage->set('cart-price-rule', $rule);
    }

    /**
     * @Given /^the (cart rule "[^"]+") is active$/
     * @Given /^the (cart rule) is active$/
     */
    public function theCartPriceRuleIsActive(CartPriceRuleInterface $rule): void
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (cart rule "[^"]+") is inactive$/
     * @Given /^the (cart rule) is inactive$/
     */
    public function theCartPriceRuleIsInActive(CartPriceRuleInterface $rule): void
    {
        $rule->setActive(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (cart rule "[^"]+") is a voucher rule$/
     * @Given /^the (cart rule) is a voucher rule$/
     * @Given /^the (cart rule) is a voucher rule with code "([^"]+)"$/
     */
    public function theCartPriceRuleIsVoucherRule(CartPriceRuleInterface $rule, $code = null): void
    {
        $rule->setIsVoucherRule(true);

        if ($code) {
            $codeObject = $this->cartPriceRuleVoucherCodeFactory->createNew();
            $codeObject->setCode($code);
            $codeObject->setCreationDate(new \DateTime());
            $codeObject->setUsed(false);
            $codeObject->setUses(0);
            $codeObject->setCartPriceRule($rule);

            $this->objectManager->persist($codeObject);
        }

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (cart rule "[^"]+") is not a voucher rule$/
     * @Given /^the (cart rule) is not a voucher rule$/
     */
    public function theCartPriceRuleIsNotAVoucherRule(CartPriceRuleInterface $rule): void
    {
        $rule->setIsVoucherRule(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition amount with value "([^"]+)" to "([^"]+)"$/
     * @Given /^the (cart rule) has a condition amount with value "([^"]+)" to "([^"]+)"$/
     */
    public function theCartPriceRuleHasAAmoundCondition(CartPriceRuleInterface $rule, $minAmount, $maxAmount): void
    {
        $this->assertConditionForm(AmountConfigurationType::class, 'amount');

        $this->addCondition($rule, $this->createConditionWithForm('amount', [
            'minAmount' => $minAmount,
            'maxAmount' => $maxAmount,
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition countries with (country "[^"]+")$/
     * @Given /^the (cart rule) has a condition countries with (country "[^"]+")$/
     */
    public function theCartPriceRuleHasACountriesCondition(CartPriceRuleInterface $rule, CountryInterface $country): void
    {
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $this->addCondition($rule, $this->createConditionWithForm('countries', [
            'countries' => [
                $country->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition customers with (customer "[^"]+")$/
     * @Given /^the (cart rule) has a condition customers with (customer "[^"]+")$/
     */
    public function theCartPriceRuleHasACustomerCondition(CartPriceRuleInterface $rule, CustomerInterface $customer): void
    {
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $this->addCondition($rule, $this->createConditionWithForm('customers', [
            'customers' => [
                $customer->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition timespan which is valid from "([^"]+") to "([^"]+)"$/
     * @Given /^the (cart rule) has a condition timespan which is valid from "([^"]+)" to "([^"]+)"$/
     */
    public function theCartPriceRuleHasATimeSpanCondition(CartPriceRuleInterface $rule, $from, $to): void
    {
        $this->assertConditionForm(TimespanConfigurationType::class, 'timespan');

        $from = new \DateTime($from);
        $to = new \DateTime($to);

        $this->addCondition($rule, $this->createConditionWithForm('timespan', [
            'dateFrom' => $from->getTimestamp() * 1000,
            'dateTo' => $to->getTimestamp() * 1000,
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition customer-groups with (customer-group "[^"]+")$/
     * @Given /^the (cart rule) has a condition customer-groups with (customer-group "[^"]+")$/
     */
    public function theCartPriceRuleHasACustomerGroupCondition(CartPriceRuleInterface $rule, CustomerGroupInterface $group): void
    {
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $this->addCondition($rule, $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [
                $group->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition stores with (store "[^"]+")$/
     * @Given /^the (cart rule) has a condition stores with (store "[^"]+")$/
     */
    public function theCartPriceRuleHasAStoreCondition(CartPriceRuleInterface $rule, StoreInterface $store): void
    {
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $this->addCondition($rule, $this->createConditionWithForm('stores', [
            'stores' => [
                $store->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition zones with (zone "[^"]+")$/
     * @Given /^the (cart rule) has a condition zones with (zone "[^"]+")$/
     */
    public function theCartPriceRuleHasAZoneCondition(CartPriceRuleInterface $rule, ZoneInterface $zone): void
    {
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $this->addCondition($rule, $this->createConditionWithForm('zones', [
            'zones' => [
                $zone->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition currencies with (currency "[^"]+")$/
     * @Given /^the (cart rule) has a condition currencies with (currency "[^"]+")$/
     */
    public function theCartPriceRuleHasACurrencyCondition(CartPriceRuleInterface $rule, CurrencyInterface $currency): void
    {
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $this->addCondition($rule, $this->createConditionWithForm('currencies', [
            'currencies' => [
                $currency->getId(),
            ],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition categories with (category "[^"]+")$/
     * @Given /^the (cart rule) has a condition categories with (category "[^"]+")$/
     */
    public function theCartPriceRuleHasACategoriesCondition(CartPriceRuleInterface $rule, CategoryInterface $category): void
    {
        $this->assertConditionForm(CategoriesConfigurationType::class, 'categories');

        $this->addCondition($rule, $this->createConditionWithForm('categories', [
            'categories' => [$category->getId()],
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition categories with (category "[^"]+") and it is recursive$/
     * @Given /^the (cart rule) has a condition categories with (category "[^"]+") and it is recursive$/
     */
    public function theCartPriceRuleHasACategoriesConditionAndItIsRecursive(CartPriceRuleInterface $rule, CategoryInterface $category): void
    {
        $this->assertConditionForm(CategoriesConfigurationType::class, 'categories');

        $this->addCondition($rule, $this->createConditionWithForm('categories', [
            'categories' => [$category->getId()],
            'recursive' => true,
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition products with (product "[^"]+")$/
     * @Given /^the (cart rule) has a condition products with (product "[^"]+")$/
     * @Given /^the (cart rule) has a condition products with (product "[^"]+") and (product "[^"]+")$/
     */
    public function theCartPriceRuleHasAProductCondition(CartPriceRuleInterface $rule, ProductInterface $product, ProductInterface $product2 = null): void
    {
        $this->assertConditionForm(ProductsConfigurationType::class, 'products');

        $configuration = [
            'products' => [
                $product->getId(),
            ],
            'include_variants' => false,
        ];

        if (null !== $product2) {
            $configuration['products'][] = $product2->getId();
        }

        $this->addCondition($rule, $this->createConditionWithForm('products', $configuration));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition products with (product "[^"]+") which includes variants$/
     * @Given /^the (cart rule) has a condition products with (product "[^"]+") which includes variants$/
     * @Given /^the (cart rule) has a condition products with (product "[^"]+") and (product "[^"]+") which includes variants$/
     */
    public function theCartPriceRuleHasAProductWithVariantsCondition(CartPriceRuleInterface $rule, ProductInterface $product, ProductInterface $product2 = null): void
    {
        $this->assertConditionForm(ProductsConfigurationType::class, 'products');

        $configuration = [
            'products' => [
                $product->getId(),
            ],
            'include_variants' => true,
        ];

        if (null !== $product2) {
            $configuration['products'][] = $product2->getId();
        }

        $this->addCondition($rule, $this->createConditionWithForm('products', $configuration));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action discount-percent with ([^"]+)% discount$/
     * @Given /^the (cart rule) has a action discount-percent with ([^"]+)% discount$/
     */
    public function theCartPriceRuleHasADiscountPercentAction(CartPriceRuleInterface $rule, $discount): void
    {
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $this->addAction($rule, $this->createActionWithForm('discountPercent', [
            'percent' => (int) $discount,
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action discount with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (cart rule) has a action discount with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (cart rule "[^"]+") has a action discount with ([^"]+) in (currency "[^"]+") off applied on ([^"]+)$/
     * @Given /^the (cart rule) has a action discount with ([^"]+) in (currency "[^"]+") off applied on ([^"]+)$/
     */
    public function theCartPriceRuleHasADiscountAmountAction(CartPriceRuleInterface $rule, $amount, CurrencyInterface $currency, string $appliedOn = 'total'): void
    {
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $this->addAction($rule, $this->createActionWithForm('discountAmount', [
            'amount' => (int) $amount,
            'currency' => $currency->getId(),
            'applyOn' => $appliedOn
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action free-shipping$/
     * @Given /^the (cart rule) has a action free-shipping$/
     */
    public function theCartPriceRuleHasAFreeShippingAction(CartPriceRuleInterface $rule): void
    {
        $this->assertActionForm(FreeShippingConfigurationType::class, 'freeShipping');

        $this->addAction($rule, $this->createActionWithForm('freeShipping'));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action gift-product with (product "[^"]+")$/
     * @Given /^the (cart rule) has a action gift-product with (product "[^"]+")$/
     */
    public function theCartPriceRuleHasAGiftProductAction(CartPriceRuleInterface $rule, ProductInterface $product): void
    {
        $this->assertActionForm(GiftProductConfigurationType::class, 'giftProduct');

        $this->addAction($rule, $this->createActionWithForm('giftProduct', [
            'product' => $product->getId(),
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action surcharge-percent with ([^"]+)% discount$/
     * @Given /^the (cart rule) has a action surcharge-percent with ([^"]+)% discount$/
     */
    public function theCartPriceRuleHasASurchargePercentAction(CartPriceRuleInterface $rule, $surcharge): void
    {
        $this->assertActionForm(SurchargePercentConfigurationType::class, 'surchargePercent');

        $this->addAction($rule, $this->createActionWithForm('surchargePercent', [
            'percent' => (int) $surcharge,
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action surcharge with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (cart rule) has a action surcharge with ([^"]+) in (currency "[^"]+") off$/
     */
    public function theCartPriceRuleHasASurchargeAmountAction(CartPriceRuleInterface $rule, $amount, CurrencyInterface $currency): void
    {
        $this->assertActionForm(SurchargeAmountConfigurationType::class, 'surchargeAmount');

        $this->addAction($rule, $this->createActionWithForm('surchargeAmount', [
            'amount' => (int) $amount,
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action voucher credit$/
     * @Given /^the (cart rule) has a action voucher credit$/
     */
    public function theCartPriceRuleHasAVoucherCreditAction(CartPriceRuleInterface $rule): void
    {
        $this->addAction($rule, $this->createActionWithForm('voucherCredit'));
    }

    /**
     * @Given /^the voucher code "([^"]+)" is a credit voucher with credit "([^"]+)" in (currency "[^"]+")$/
     * @Given /^the voucher code "([^"]+)" is a credit voucher with credit "([^"]+)" in (currency "[^"]+") and credit used "([^"]+)"$/
     */
    public function theVoucherCodeIsACreditCodeWithCreditInCurrency($voucherCode, int $credit, CurrencyInterface $currency, int $used = null): void
    {
        $voucherCode = $this->cartPriceRuleVoucherRepository->findByCode($voucherCode);

        /**
         * @var $voucherCode   CartPriceRuleVoucherCodeInterface
         * @var $cartPriceRule CartPriceRuleInterface
         */
        Assert::isInstanceOf($voucherCode, CartPriceRuleVoucherCodeInterface::class);

        $voucherCode->setIsCreditCode(true);
        $voucherCode->setCreditAvailable($credit);
        $voucherCode->setCreditCurrency($currency);

        if (null !== $used) {
            $voucherCode->setCreditUsed($used);
        }

        $this->objectManager->persist($voucherCode);
        $this->objectManager->flush();
    }

    /**
     * @param CartPriceRuleInterface $rule
     * @param ConditionInterface     $condition
     */
    private function addCondition(CartPriceRuleInterface $rule, ConditionInterface $condition): void
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param CartPriceRuleInterface $rule
     * @param ActionInterface        $action
     */
    private function addAction(CartPriceRuleInterface $rule, ActionInterface $action): void
    {
        $rule->addAction($action);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    protected function getConditionFormRegistry(): FormTypeRegistryInterface
    {
        return $this->conditionFormTypeRegistry;
    }

    protected function getConditionFormClass(): string
    {
        return CartPriceRuleConditionType::class;
    }

    protected function getActionFormRegistry(): FormTypeRegistryInterface
    {
        return $this->actionFormTypeRegistry;
    }

    protected function getActionFormClass(): string
    {
        return CartPriceRuleActionType::class;
    }

    protected function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }
}
