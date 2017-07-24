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

namespace CoreShop\Test;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Pimcore\File;
use Pimcore\Model\Object\Service;

/**
 * @TODO: This should be created using fixtures!!
 */
class Data
{
    /**
     * @var ProductInterface
     */
    public static $product1;

    /**
     * @var ProductInterface
     */
    public static $product2;

    /**
     * @var ProductInterface
     */
    public static $product3;

    /**
     * @var CategoryInterface
     */
    public static $category1;

    /**
     * @var CategoryInterface
     */
    public static $category2;

    /**
     * @var CarrierInterface
     */
    public static $carrier1;

    /**
     * @var CarrierInterface
     */
    public static $carrier2;

    /**
     * @var TaxRuleGroupInterface
     */
    public static $taxRuleGroup;

    /**
     * @var AddressInterface
     */
    public static $address;

    /**
     * @var CustomerInterface
     */
    public static $customer1;

    /**
     * @var CustomerGroupInterface
     */
    public static $customerGroup1;

    /**
     * @var CustomerGroupInterface
     */
    public static $customerGroup2;

    /**
     * @var StoreInterface
     */
    public static $store;

    /**
     * @param $serviceId
     *
     * @return object
     */
    private static function get($serviceId)
    {
        return \Pimcore::getKernel()->getContainer()->get($serviceId);
    }

    /**
     * Create Test Data.
     */
    public static function createData()
    {
        self::$store = $standardStore = self::get('coreshop.repository.store')->findStandard();

        self::createTaxRule();
        self::createTestCarrierWeight();
        //self::createTestCarrierPrice();
        self::createTestProduct();
        self::createCustomerGroups();
        self::createCustomer();
        //\CoreShop::getTools()->setUser(self::$customer1);

        self::get('coreshop.context.country.fixed')->setCountry(self::$store->getBaseCountry());
        self::get('coreshop.context.currency.fixed')->setCurrency(self::$store->getCurrency());
        self::get('coreshop.context.store.fixed')->setStore(self::$store);
        self::get('coreshop.context.customer.fixed')->setCustomer(self::$customer1);
        self::get('coreshop.context.locale.fixed')->setLocale('en');
    }

    /**
     * Create Test Tax Rules.
     */
    public static function createTaxRule()
    {
        if (!self::$taxRuleGroup instanceof TaxRuleGroupInterface) {
            $taxRuleGroupFactory = self::get('coreshop.factory.tax_rule_group');
            $taxRuleFactory = self::get('coreshop.factory.tax_rule');
            $taxRateFactory = self::get('coreshop.factory.tax_rate');
            $entityManager = self::get('doctrine.orm.entity_manager');

            /**
             * @var TaxRateInterface
             */
            $taxRate = $taxRateFactory->createNew();
            $taxRate->setRate(20);
            $taxRate->setName('20');
            $taxRate->setActive(true);

            /**
             * @var TaxRuleGroupInterface
             */
            $taxRuleGroup = $taxRuleGroupFactory->createNew();
            $taxRuleGroup->setName('20');
            $taxRuleGroup->setActive(true);
            $taxRuleGroup->addStore(self::$store);

            /**
             * @var TaxRuleInterface
             */
            $taxRule = $taxRuleFactory->createNew();
            $taxRule->setTaxRuleGroup($taxRuleGroup);
            $taxRule->setTaxRate($taxRate);
            $taxRule->setBehavior(TaxCalculatorInterface::DISABLE_METHOD);
            $taxRule->setCountry(self::$store->getBaseCountry());
            $taxRule->setState(null);

            $entityManager->persist($taxRate);
            $entityManager->persist($taxRuleGroup);
            $entityManager->persist($taxRule);
            $entityManager->flush();

            self::$taxRuleGroup = $taxRuleGroup;
        }
    }

    /**
     * Create Test Carriers.
     */
    public static function createTestCarrierWeight()
    {
        if (!self::$carrier1 instanceof CarrierInterface) {
            $carrierFactory = self::get('coreshop.factory.carrier');
            $entityManager = self::get('doctrine.orm.entity_manager');
            $conditionFactory = self::get('coreshop.factory.rule_condition');
            $actionFactory = self::get('coreshop.factory.rule_action');
            $shippingRuleFactory = self::get('coreshop.factory.shipping_rule');
            $shippingRuleGroupFactory = self::get('coreshop.factory.shipping_rule_group');

            /**
             * @var CarrierInterface
             */
            $carrier = $carrierFactory->createNew();
            $carrier->setName('Test-Carrier-Weight');
            $carrier->setLabel('Test-Carrier-Weight');
            $carrier->setRangeBehaviour(CarrierInterface::RANGE_BEHAVIOUR_DEACTIVATE);
            $carrier->setTaxRule(self::$taxRuleGroup);
            $carrier->setIsFree(false);
            $carrier->addStore(self::$store);

            $entityManager->persist($carrier);

            /**
             * @var ConditionInterface
             * @var $weightCond        ConditionInterface
             * @var $priceAct          ActionInterface
             */
            $zoneCond = $conditionFactory->createNew();
            $zoneCond->setType('zones');
            $zoneCond->setConfiguration(['zones' => [self::$store->getBaseCountry()->getZone()->getId()]]);

            $weightCond = $conditionFactory->createNew();
            $weightCond->setType('weight');
            $weightCond->setConfiguration(['minWeight' => 0, 'maxWeight' => 5000]);

            $priceAct = $actionFactory->createNew();
            $priceAct->setType('price');
            $priceAct->setConfiguration(['price' => 1000]);

            /**
             * @var ShippingRuleInterface
             */
            $rule1 = $shippingRuleFactory->createNew();
            $rule1->setName('carrier1-rule');
            $rule1->addAction($priceAct);
            $rule1->addCondition($weightCond);
            $rule1->addCondition($zoneCond);

            /**
             * @var ShippingRuleGroupInterface
             */
            $ruleGroup = $shippingRuleGroupFactory->createNew();
            $ruleGroup->setCarrier($carrier);
            $ruleGroup->setShippingRule($rule1);
            $ruleGroup->setPriority(1);

            $carrier->addShippingRule($ruleGroup);

            $entityManager->persist($rule1);
            $entityManager->persist($ruleGroup);
            $entityManager->persist($carrier);
            $entityManager->flush();

            self::$carrier1 = $carrier;
        }
    }

    /**
     * Create Test Products.
     */
    public static function createTestProduct()
    {
        $productFactory = self::get('coreshop.factory.product');
        $categoryFactory = self::get('coreshop.factory.category');

        if (!self::$category1 instanceof CategoryInterface) {
            /**
             * @var CategoryInterface
             */
            $category1 = $categoryFactory->createNew();
            $category1->setName('test');
            $category1->setKey('test-category');
            $category1->setParent(Service::createFolderByPath('/coreshop/categories'));
            $category1->save();

            self::$category1 = $category1;
        }

        if (!self::$category2 instanceof CategoryInterface) {
            /**
             * @var CategoryInterface
             */
            $category2 = $categoryFactory->createNew();
            $category2->setName('test2');
            $category2->setKey('test-category2');
            $category2->setParent(Service::createFolderByPath('/coreshop/categories'));
            $category2->save();

            self::$category2 = $category2;
        }

        if (!self::$product1 instanceof ProductInterface) {
            /**
             * @var ProductInterface
             */
            $product1 = $productFactory->createNew();
            $product1->setName('test1');
            $product1->setWholesalePrice(1000);
            $product1->setPrice(1500);
            $product1->setCategories([self::$category1]);
            $product1->setHeight(50);
            $product1->setWidth(50);
            $product1->setDepth(50);
            $product1->setWeight(50);
            $product1->setTaxRule(self::$taxRuleGroup);
            $product1->setParent(Service::createFolderByPath('/coreshop/products'));
            $product1->setKey(File::getValidFilename('test1'));
            //$product1->setStore([Shop::getDefaultShop()->getId()]);
            $product1->save();

            self::$product1 = $product1;
        }

        if (!self::$product2 instanceof ProductInterface) {
            /**
             * @var ProductInterface
             */
            $product2 = $productFactory->createNew();
            $product2->setName('test2');
            $product2->setWholesalePrice(10000);
            $product2->setPrice(15000);
            $product2->setCategories([self::$category2]);
            $product2->setHeight(500);
            $product2->setWidth(500);
            $product2->setDepth(500);
            $product2->setWeight(500);
            $product2->setTaxRule(self::$taxRuleGroup);
            $product2->setParent(Service::createFolderByPath('/coreshop/products'));
            $product2->setKey(File::getValidFilename('test2'));
            //$product2->setShops([Shop::getDefaultShop()->getId()]);
            $product2->save();

            self::$product2 = $product2;
        }

        if (!self::$product3 instanceof ProductInterface) {
            /**
             * @var ProductInterface
             */
            $product3 = $productFactory->createNew();
            $product3->setName('test3');
            $product3->setWholesalePrice(5000);
            $product3->setPrice(7500);
            $product3->setHeight(100);
            $product3->setWidth(100);
            $product3->setDepth(100);
            $product3->setWeight(100);
            $product3->setTaxRule(self::$taxRuleGroup);
            $product3->setParent(Service::createFolderByPath('/coreshop/products'));
            $product3->setKey(File::getValidFilename('test3'));
            //$product3->setShops([Shop::getDefaultShop()->getId()]);
            $product3->save();

            self::$product3 = $product3;
        }
    }

    /**
     * @return CartInterface
     */
    public static function createCart()
    {
        $cart = self::get('coreshop.factory.cart')->createNew();
        $cart->setKey(uniqid());
        $cart->setParent(Service::createFolderByPath('/'));

        return $cart;
    }

    /**
     * @return CartInterface
     */
    public static function createCartWithProducts()
    {
        $cart = self::createCart();

        self::get('coreshop.cart.modifier')->addCartItem($cart, self::$product1);
        self::get('coreshop.cart.modifier')->addCartItem($cart, self::$product2);
        self::get('coreshop.cart.modifier')->addCartItem($cart, self::$product3);

        $cart->setShippingAddress(self::$address);
        $cart->setInvoiceAddress(self::$address);

        return $cart;
    }

    /**
     * Create Test Customer Groups.
     */
    public static function createCustomerGroups()
    {
        $customerGroupFactory = self::get('coreshop.factory.customer_group');

        if (!self::$customerGroup1 instanceof CustomerGroupInterface) {
            /**
             * @var CustomerGroupInterface
             */
            $customerGroup1 = $customerGroupFactory->createNew();
            $customerGroup1->setName('Group1');
            $customerGroup1->setShops([self::$store->getId()]);
            $customerGroup1->setKey('group1');
            $customerGroup1->setParent(Service::createFolderByPath('/customer-groups'));
            $customerGroup1->save();

            self::$customerGroup1 = $customerGroup1;
        }

        if (!self::$customerGroup2 instanceof CustomerGroupInterface) {
            /**
             * @var CustomerGroupInterface
             */
            $customerGroup2 = $customerGroupFactory->createNew();
            $customerGroup2->setName('Group2');
            $customerGroup2->setShops([self::$store->getId()]);
            $customerGroup2->setKey('group2');
            $customerGroup2->setParent(Service::createFolderByPath('/customer-groups'));
            $customerGroup2->save();

            self::$customerGroup2 = $customerGroup2;
        }
    }

    /**
     * Create Test Customer.
     */
    public static function createCustomer()
    {
        $customerFactory = self::get('coreshop.factory.customer');
        $addressFactory = self::get('coreshop.factory.address');

        if (!self::$customer1 instanceof CustomerInterface) {
            /**
             * @var CustomerInterface
             */
            $customer = $customerFactory->createNew();
            $customer->setKey('customer1');
            $customer->setParent(Service::createFolderByPath('/users'));
            $customer->setFirstname('Max');
            $customer->setLastname('Mustermann');
            $customer->setGender("m");
            $customer->setEmail('mus@coreshop.org');
            $customer->setCustomerGroups([self::$customerGroup1]);
            $customer->save();

            /**
             * @var AddressInterface
             */
            $address = $addressFactory->createNew();
            $address->setCity('Wels');
            $address->setCountry(self::$store->getBaseCountry());
            $address->setStreet('Freiung 9-11/N3');
            $address->setPostcode('4600');
            $address->setFirstname('Dominik');
            $address->setLastname('Pfaffenbauer');
            $address->setKey('test-address-customer1');
            $address->setParent(Service::createFolderByPath('/'));
            $address->save();

            $customer->setAddresses([$address]);
            $customer->save();

            self::$customer1 = $customer;
            self::$address = $address;
        }
    }
}
