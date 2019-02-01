<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\AmountConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\CartInterface;
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
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Webmozart\Assert\Assert;

final class CartPriceRuleContext implements Context
{
    use ConditionFormTrait;
    use ActionFormTrait;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormTypeRegistryInterface
     */
    private $conditionFormTypeRegistry;

    /**
     * @var FormTypeRegistryInterface
     */
    private $actionFormTypeRegistry;

    /**
     * @var FactoryInterface
     */
    private $cartPriceRuleFactory;

    /**
     * @var CartPriceRuleRepositoryInterface
     */
    private $cartPriceRuleRepository;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $cartPriceRuleVoucherRepository;

    /**
     * @var CartPriceRuleProcessorInterface
     */
    private $cartPriceRuleProcessor;

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var FactoryInterface
     */
    private $cartPriceRuleVoucherCodeFactory;

    /**
     * @param SharedStorageInterface                  $sharedStorage
     * @param ObjectManager                           $objectManager
     * @param FormFactoryInterface                    $formFactory
     * @param FormTypeRegistryInterface               $conditionFormTypeRegistry
     * @param FormTypeRegistryInterface               $actionFormTypeRegistry
     * @param FactoryInterface                        $cartPriceRuleFactory
     * @param CartPriceRuleRepositoryInterface        $cartPriceRuleRepository
     * @param CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherRepository
     * @param CartPriceRuleProcessorInterface         $cartPriceRuleProcessor
     * @param CartManagerInterface                    $cartManager
     * @param FactoryInterface                        $cartPriceRuleVoucherCodeFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $conditionFormTypeRegistry,
        FormTypeRegistryInterface $actionFormTypeRegistry,
        FactoryInterface $cartPriceRuleFactory,
        CartPriceRuleRepositoryInterface $cartPriceRuleRepository,
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
        $this->cartPriceRuleRepository = $cartPriceRuleRepository;
        $this->cartPriceRuleVoucherRepository = $cartPriceRuleVoucherRepository;
        $this->cartPriceRuleProcessor = $cartPriceRuleProcessor;
        $this->cartManager = $cartManager;
        $this->cartPriceRuleVoucherCodeFactory = $cartPriceRuleVoucherCodeFactory;
    }

    /**
     * @Given /^I apply the voucher code "([^"]+)" to (my cart)$/
     */
    public function iApplyTheCartRuleToMyCart($voucherCode, CartInterface $cart)
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
    public function addingACartPriceRule($ruleName)
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
    public function theCartPriceRuleIsActive(CartPriceRuleInterface $rule)
    {
        $rule->setActive(true);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (cart rule "[^"]+") is inactive$/
     * @Given /^the (cart rule) is inactive$/
     */
    public function theCartPriceRuleIsInActive(CartPriceRuleInterface $rule)
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
    public function theCartPriceRuleIsVoucherRule(CartPriceRuleInterface $rule, $code = null)
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
    public function theCartPriceRuleIsNotAVoucherRule(CartPriceRuleInterface $rule)
    {
        $rule->setIsVoucherRule(false);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a condition amount with value "([^"]+)" to "([^"]+)"$/
     * @Given /^the (cart rule) has a condition amount with value "([^"]+)" to "([^"]+)"$/
     */
    public function theCartPriceRuleHasAAmoundCondition(CartPriceRuleInterface $rule, $minAmount, $maxAmount)
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
    public function theCartPriceRuleHasACountriesCondition(CartPriceRuleInterface $rule, CountryInterface $country)
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
    public function theCartPriceRuleHasACustomerCondition(CartPriceRuleInterface $rule, CustomerInterface $customer)
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
    public function theCartPriceRuleHasATimeSpanCondition(CartPriceRuleInterface $rule, $from, $to)
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
    public function theCartPriceRuleHasACustomerGroupCondition(CartPriceRuleInterface $rule, CustomerGroupInterface $group)
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
    public function theCartPriceRuleHasAStoreCondition(CartPriceRuleInterface $rule, StoreInterface $store)
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
    public function theCartPriceRuleHasAZoneCondition(CartPriceRuleInterface $rule, ZoneInterface $zone)
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
    public function theCartPriceRuleHasACurrencyCondition(CartPriceRuleInterface $rule, CurrencyInterface $currency)
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
    public function theCartPriceRuleHasACategoriesCondition(CartPriceRuleInterface $rule, CategoryInterface $category)
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
    public function theCartPriceRuleHasACategoriesConditionAndItIsRecursive(CartPriceRuleInterface $rule, CategoryInterface $category)
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
    public function theCartPriceRuleHasAProductCondition(CartPriceRuleInterface $rule, ProductInterface $product, ProductInterface $product2 = null)
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
    public function theCartPriceRuleHasAProductWithVariantsCondition(CartPriceRuleInterface $rule, ProductInterface $product, ProductInterface $product2 = null)
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
    public function theCartPriceRuleHasADiscountPercentAction(CartPriceRuleInterface $rule, $discount)
    {
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $this->addAction($rule, $this->createActionWithForm('discountPercent', [
            'percent' => intval($discount),
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action discount with ([^"]+) in (currency "[^"]+") off$/
     * @Given /^the (cart rule) has a action discount with ([^"]+) in (currency "[^"]+") off$/
     */
    public function theCartPriceRuleHasADiscountAmountAction(CartPriceRuleInterface $rule, $amount, CurrencyInterface $currency)
    {
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $this->addAction($rule, $this->createActionWithForm('discountAmount', [
            'amount' => intval($amount),
            'currency' => $currency->getId(),
        ]));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action free-shipping$/
     * @Given /^the (cart rule) has a action free-shipping$/
     */
    public function theCartPriceRuleHasAFreeShippingAction(CartPriceRuleInterface $rule)
    {
        $this->assertActionForm(FreeShippingConfigurationType::class, 'freeShipping');

        $this->addAction($rule, $this->createActionWithForm('freeShipping'));
    }

    /**
     * @Given /^the (cart rule "[^"]+") has a action gift-product with (product "[^"]+")$/
     * @Given /^the (cart rule) has a action gift-product with (product "[^"]+")$/
     */
    public function theCartPriceRuleHasAGiftProductAction(CartPriceRuleInterface $rule, ProductInterface $product)
    {
        $this->assertActionForm(GiftProductConfigurationType::class, 'giftProduct');

        $this->addAction($rule, $this->createActionWithForm('giftProduct', [
            'product' => $product->getId(),
        ]));
    }

    /**
     * @param CartPriceRuleInterface $rule
     * @param ConditionInterface     $condition
     */
    private function addCondition(CartPriceRuleInterface $rule, ConditionInterface $condition)
    {
        $rule->addCondition($condition);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * @param CartPriceRuleInterface $rule
     * @param ActionInterface        $action
     */
    private function addAction(CartPriceRuleInterface $rule, ActionInterface $action)
    {
        $rule->addAction($action);

        $this->objectManager->persist($rule);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistry()
    {
        return $this->conditionFormTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return CartPriceRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistry()
    {
        return $this->actionFormTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return CartPriceRuleActionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormFactory()
    {
        return $this->formFactory;
    }
}
