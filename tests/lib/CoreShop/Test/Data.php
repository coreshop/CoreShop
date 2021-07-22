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

namespace CoreShop\Test;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\Product;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Service;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Data
{
    /**
     * @var Product
     */
    public static $product1;

    /**
     * @var Product
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
     * @var StoreInterface
     */
    public static $storeGrossPrices;

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
        //Clear Database
        $purger = new PurgeDatabase(\Pimcore::getContainer()->get('doctrine.orm.entity_manager'));
        $purger->purge();

        //Install Fixtures
        $parameters = array_merge(
            ['command' => 'coreshop:install:fixtures']
        );

        $application = new Application(\Pimcore::getKernel());
        $application->setAutoExit(false);
        $application->run(new ArrayInput($parameters), new ConsoleOutput());

        self::$store = $standardStore = self::get('coreshop.repository.store')->findStandard();

        self::createGrossStore();
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

        self::get('security.token_storage')->setToken(new UsernamePasswordToken('unit-tests', 'unit-test', 'unit-test'));
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
             * @var $taxRate TaxRateInterface
             */
            $taxRate = $taxRateFactory->createNew();
            $taxRate->setRate(20);
            $taxRate->setName('20');
            $taxRate->setActive(true);

            /**
             * @var $taxRuleGroup TaxRuleGroupInterface
             */
            $taxRuleGroup = $taxRuleGroupFactory->createNew();
            $taxRuleGroup->setName('20');
            $taxRuleGroup->setActive(true);

            /**
             * @var $taxRule TaxRuleInterface
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
             * @var $carrier CarrierInterface
             */
            $carrier = $carrierFactory->createNew();
            $carrier->setIdentifier('Test-Carrier-Weight');
            $carrier->setTitle('Test-Carrier-Weight', 'en');
            $carrier->setTaxRule(self::$taxRuleGroup);
            $carrier->setIsFree(false);
            $carrier->setDescription('TEST', 'en');
            $carrier->addStore(self::$store);
            $carrier->addStore(self::$storeGrossPrices);

            $entityManager->persist($carrier);

            /**
             * @var $zoneCond ConditionInterface
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
            $priceAct->setConfiguration(['price' => 1000, 'currency' => self::$store->getCurrency()->getId()]);

            /**
             * @var $rule1 ShippingRuleInterface
             */
            $rule1 = $shippingRuleFactory->createNew();
            $rule1->setActive(true);
            $rule1->setName('carrier1-rule');
            $rule1->addAction($priceAct);
            $rule1->addCondition($weightCond);
            $rule1->addCondition($zoneCond);

            /**
             * @var $ruleGroup ShippingRuleGroupInterface
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
             * @var $category1 CategoryInterface
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
             * @var $category2 CategoryInterface
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
             * @var $product1 ProductInterface
             */
            $product1 = $productFactory->createNew();
            $product1->setName('test1');
            $product1->setWholesalePrice(1000);
            $product1->setStorePrice(1500, static::$store);
            $product1->setStorePrice(1800, static::$storeGrossPrices);
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
             * @var $product2 ProductInterface
             */
            $product2 = $productFactory->createNew();
            $product2->setName('test2');
            $product2->setWholesalePrice(10000);
            $product2->setStorePrice(15000, static::$store);
            $product2->setStorePrice(18000, static::$storeGrossPrices);
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
             * @var $product3 ProductInterface
             */
            $product3 = $productFactory->createNew();
            $product3->setName('test3');
            $product3->setWholesalePrice(5000);
            $product3->setStorePrice(7500, static::$store);
            $product3->setStorePrice(6250, static::$storeGrossPrices);
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
        $cart->setCustomer(self::get('coreshop.context.customer')->getCustomer());
        $cart->setStore(self::get('coreshop.context.store')->getStore());
        $cart->setCurrency(Data::$store->getCurrency());

        self::get('coreshop.cart.manager')->persistCart($cart);

        return $cart;
    }

    /**
     * @return CartInterface
     */
    public static function createCartWithProducts()
    {
        $cart = self::createCart();
        $factory = self::get('coreshop.factory.cart_item');

        self::get('coreshop.cart.modifier')->addToList($cart, $factory->createWithPurchasable(self::$product1));
        self::get('coreshop.cart.modifier')->addToList($cart, $factory->createWithPurchasable(self::$product2));
        self::get('coreshop.cart.modifier')->addToList($cart, $factory->createWithPurchasable(self::$product3));

        $cart->setShippingAddress(self::$address);
        $cart->setInvoiceAddress(self::$address);

        self::get('coreshop.cart.manager')->persistCart($cart);

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
             * @var $customerGroup1 CustomerGroupInterface
             */
            $customerGroup1 = $customerGroupFactory->createNew();
            $customerGroup1->setName('Group1');
            $customerGroup1->setKey('group1');
            $customerGroup1->setParent(Service::createFolderByPath('/customer-groups'));
            $customerGroup1->save();

            self::$customerGroup1 = $customerGroup1;
        }

        if (!self::$customerGroup2 instanceof CustomerGroupInterface) {
            /**
             * @var $customerGroup2 CustomerGroupInterface
             */
            $customerGroup2 = $customerGroupFactory->createNew();
            $customerGroup2->setName('Group2');
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
             * @var $customer CustomerInterface
             */
            $customer = $customerFactory->createNew();
            $customer->setKey('customer1');
            $customer->setParent(Service::createFolderByPath('/users'));
            $customer->setFirstname('Max');
            $customer->setLastname('Mustermann');
            $customer->setGender("m");
            $customer->setEmail('info@coreshop.org');
            $customer->setCustomerGroups([self::$customerGroup1]);
            $customer->save();

            /**
             * @var $address AddressInterface
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

            $customer->setDefaultAddress($address);
            $customer->addAddress($address);
            $customer->save();

            self::$customer1 = $customer;
            self::$address = $address;
        }
    }

    public static function createGrossStore()
    {
        /**
         * @var $grossStore StoreInterface
         */
        $grossStore = self::get('coreshop.factory.store')->createNew();;
        $grossStore->setCurrency(self::get('coreshop.repository.currency')->getByCode('EUR'));
        $grossStore->setBaseCountry(self::get('coreshop.repository.country')->findByCode('AT'));
        $grossStore->setName('GrossPrice');
        $grossStore->setUseGrossPrice(true);

        self::$storeGrossPrices = $grossStore;

        $entityManager = self::get('doctrine.orm.entity_manager');
        $entityManager->persist($grossStore);
        $entityManager->flush();
    }
}
