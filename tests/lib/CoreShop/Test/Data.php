<?php

namespace CoreShop\Test;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use Pimcore\File;
use Pimcore\Model\Object\Service;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @var KernelInterface
     */
    public static $kernel;

    /**
     * @param $serviceId
     * @return object
     */
    private static function get($serviceId) {
        return \Pimcore::getKernel()->getContainer()->get($serviceId);
    }

    /**
     * Create Test Data
     */
    public static function createData()
    {
        //Configuration::set("SYSTEM.BASE.COUNTRY", Country::getById(2)->getId());

        //$session = \CoreShop::getTools()->getSession();
        //$session->countryId = Country::getById(2)->getId();

        //$session->stateId = State::getById(23)->getId();

        self::$store = $standardStore = self::get('coreshop.repository.store')->findStandard();

        self::createTaxRule();
        self::createTestCarrierWeight();
        //self::createTestCarrierPrice();
        self::createTestProduct();
        self::createCustomerGroups();
        self::createCustomer();

        self::get("doctrine.orm.entity_manager")->flush();
        //\CoreShop::getTools()->setUser(self::$customer1);
    }

    /**
     * Create Test Tax Rules
     */
    public static function createTaxRule()
    {
        if (!self::$taxRuleGroup instanceof TaxRuleGroupInterface) {
            $taxRuleGroupFactory = self::get("coreshop.factory.tax_rule_group");
            $taxRuleFactory = self::get("coreshop.factory.tax_rule");
            $taxRateFactory = self::get("coreshop.factory.tax_rate");
            $entityManager = self::get("doctrine.orm.entity_manager");

            /**
             * @var $taxRate TaxRateInterface
             */
            $taxRate = $taxRateFactory->createNew();
            $taxRate->setRate(20);
            $taxRate->setName("20");
            $taxRate->setActive(true);

            /**
             * @var $taxRuleGroup TaxRuleGroupInterface
             */
            $taxRuleGroup = $taxRuleGroupFactory->createNew();
            $taxRuleGroup->setName("20");
            $taxRuleGroup->setActive(true);
            $taxRuleGroup->addStore(self::$store);

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

            self::$taxRuleGroup = $taxRuleGroup;
        }
    }

    /**
     * Create Test Carriers
     */
    public static function createTestCarrierWeight()
    {
        if (!self::$carrier1 instanceof CarrierInterface) {
            $carrierFactory = self::get("coreshop.factory.carrier");
            $entityManager = self::get("doctrine.orm.entity_manager");
            $conditionFactory = self::get('coreshop.factory.rule_condition');
            $actionFactory = self::get('coreshop.factory.rule_action');
            $shippingRuleFactory = self::get('coreshop.factory.shipping_rule');
            $shippingRuleGroupFactory = self::get('coreshop.factory.shipping_rule_group');

            /**
             * @var $carrier CarrierInterface
             */
            $carrier = $carrierFactory->createNew();
            $carrier->setName("Test-Carrier-Weight");
            $carrier->setLabel("Test-Carrier-Weight");
            $carrier->setRangeBehaviour(CarrierInterface::RANGE_BEHAVIOUR_DEACTIVATE);
            $carrier->setTaxRule(self::$taxRuleGroup);
            $carrier->setIsFree(false);
            $carrier->addStore(self::$store);

            $entityManager->persist($carrier);


            /**
             * @var $zoneCond ConditionInterface
             * @var $weightCond ConditionInterface
             * @var $priceAct ActionInterface
             */
            $zoneCond = $conditionFactory->createNew();
            $zoneCond->setType('zones');
            $zoneCond->setConfiguration(['zones' => [1]]);

            $weightCond = $conditionFactory->createNew();
            $weightCond->setType('weight');
            $weightCond->setConfiguration(['minWeight' => 0, 'maxWeight' => 5000]);

            $priceAct = $actionFactory->createNew();
            $priceAct->setType('price');
            $priceAct->setConfiguration(['price' => 10]);

            /**
             * @var $rule1 ShippingRuleInterface
             */
            $rule1 = $shippingRuleFactory->createNew();
            $rule1->setName("carrier1-rule");
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

            $entityManager->persist($rule1);
            $entityManager->persist($ruleGroup);

            self::$carrier1 = $carrier;
        }
    }

    /**
     * Create Test Products
     */
    public static function createTestProduct()
    {
        $productFactory = self::get('coreshop.factory.product');

        if (!self::$product1 instanceof ProductInterface) {
            /**
             * @var $product1 ProductInterface
             */
            $product1 = $productFactory->createNew();
            $product1->setName("test1");
            $product1->setWholesalePrice(10);
            $product1->setBasePrice(15);
            //$product1->setHeight(50);
            //$product1->setWidth(50);
            //$product1->setDepth(50);
            $product1->setWeight(50);
            $product1->setTaxRule(self::$taxRuleGroup);
            $product1->setParent(Service::createFolderByPath("/coreshop/products"));
            $product1->setKey(File::getValidFilename("test1"));
            //$product1->addStore([Shop::getDefaultShop()->getId()]);
            $product1->save();

            self::$product1 = $product1;
        }

        if (!self::$product2 instanceof ProductInterface) {
            /**
             * @var $product2 ProductInterface
             */
            $product2 = $productFactory->createNew();
            $product2->setName("test2");
            $product2->setWholesalePrice(100);
            $product2->setBasePrice(150);
            //$product2->setHeight(500);
            //$product2->setWidth(500);
            //$product2->setDepth(500);
            $product2->setWeight(500);
            $product2->setTaxRule(self::$taxRuleGroup);
            $product2->setParent(Service::createFolderByPath("/coreshop/products"));
            $product2->setKey(File::getValidFilename("test2"));
            //$product2->setShops([Shop::getDefaultShop()->getId()]);
            $product2->save();

            self::$product2 = $product2;
        }

        if (!self::$product3 instanceof ProductInterface) {
             /**
             * @var $product3 ProductInterface
             */
            $product3 = $productFactory->createNew();
            $product3->setName("test3");
            $product3->setWholesalePrice(50);
            $product3->setBasePrice(75);
            //$product3->setHeight(100);
            //$product3->setWidth(100);
            //$product3->setDepth(100);
            $product3->setWeight(100);
            $product3->setTaxRule(self::$taxRuleGroup);
            $product3->setParent(Service::createFolderByPath("/coreshop/products"));
            $product3->setKey(File::getValidFilename("test3"));
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
        return self::get('coreshop.cart.manager')->getCart();
    }

    /**
     * @return CartInterface
     */
    public static function createCartWithProducts()
    {
        $cart = self::createCart();

        $cart->addItem(self::$product1);
        $cart->addItem(self::$product2);
        $cart->addItem(self::$product3);

        return $cart;
    }

    /**
     * Create Test Customer Groups
     */
    public static function createCustomerGroups()
    {
        $customerGroupFactory = self::get('coreshop.factory.customer_group');

        if (!self::$customerGroup1 instanceof CustomerGroupInterface) {
            /**
             * @var $customerGroup1 CustomerGroupInterface
             */
            $customerGroup1 = $customerGroupFactory->createNew();
            $customerGroup1->setName("Group1");
            $customerGroup1->setShops([self::$store->getId()]);
            $customerGroup1->setKey("group1");
            $customerGroup1->setParent(Service::createFolderByPath("/customer-groups"));
            $customerGroup1->save();

            self::$customerGroup1 = $customerGroup1;
        }

        if (!self::$customerGroup2 instanceof CustomerGroupInterface) {
            /**
             * @var $customerGroup2 CustomerGroupInterface
             */
            $customerGroup2 = $customerGroupFactory->createNew();
            $customerGroup2->setName("Group2");
            $customerGroup2->setShops([self::$store->getId()]);
            $customerGroup2->setKey("group2");
            $customerGroup2->setParent(Service::createFolderByPath("/customer-groups"));
            $customerGroup2->save();

            self::$customerGroup2 = $customerGroup2;
        }
    }

    /**
     * Create Test Customer
     */
    public static function createCustomer()
    {
        $customerFactory = self::get('coreshop.factory.customer');


        if (!self::$customer1 instanceof CustomerInterface) {
            /**
             * @var $customer CustomerInterface
             */
            $customer = $customerFactory->createNew();
            $customer->setKey("customer1");
            $customer->setParent(Service::createFolderByPath("/users"));
            $customer->setFirstname("customer");
            $customer->setLastname("1");
            $customer->setGender("m");
            $customer->setEmail("test@coreshop.org");
            $customer->setCustomerGroups([self::$customerGroup1]);
            $customer->save();

            self::$customer1 = $customer;
        }
    }
}
