<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test;

use CoreShop\Model\Carrier;
use CoreShop\Model\Configuration;
use CoreShop\Model\Country;
use CoreShop\Model\Customer\Group;
use CoreShop\Model\Product;
use CoreShop\Model\Shop;
use CoreShop\Model\State;
use CoreShop\Model\Tax;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\TaxRule;
use CoreShop\Model\TaxRuleGroup;
use CoreShop\Model\User;
use CoreShop\Model\Cart;
use CoreShop\Tool;
use Pimcore\File;
use Pimcore\Model\Object\Service;

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
     * @var Product
     */
    public static $product3;

    /**
     * @var Carrier
     */
    public static $carrier1;

    /**
     * @var Carrier
     */
    public static $carrier2;

    /**
     * @var TaxRuleGroup
     */
    public static $taxRuleGroup;

    /**
     * @var User
     */
    public static $customer1;

    /**
     * @var Group
     */
    public static $customerGroup1;

    /**
     * @var Group
     */
    public static $customerGroup2;

    /**
     * @var Shop
     */
    public static $shop;

    public static function createData()
    {
        Configuration::set("SYSTEM.BASE.COUNTRY", Country::getById(2)->getId());

        $session = \CoreShop::getTools()->getSession();
        $session->countryId = Country::getById(2)->getId();

        $session->stateId = State::getById(23)->getId();

        self::createTaxRule();
        self::createTestCarrierWeight();
        self::createTestCarrierPrice();
        self::createTestProduct();
        self::createCustomerGroups();
        self::createCustomer();
        self::createShop();

        Tool::setUser(self::$customer1);
    }

    public static function createTaxRule()
    {
        if (!self::$taxRuleGroup instanceof TaxRuleGroup) {
            $tax = new Tax();
            $tax->setRate(20);
            $tax->setName("20");
            $tax->setActive(true);
            $tax->save();

            $taxRuleGroup = new TaxRuleGroup();
            $taxRuleGroup->setName("20");
            $taxRuleGroup->setActive(true);
            $taxRuleGroup->setShopIds(array(Shop::getDefaultShop()->getId()));
            $taxRuleGroup->save();

            $taxRule = new TaxRule();
            $taxRule->setTaxRuleGroup($taxRuleGroup);
            $taxRule->setTax($tax);
            $taxRule->setBehavior(TaxCalculator::DISABLE_METHOD);
            $taxRule->setCountry(Country::getById(2)); //Austria
            $taxRule->setStateId(0); //Upper Austria
            $taxRule->save();

            self::$taxRuleGroup = $taxRuleGroup;
        }
    }

    public static function createTestCarrierWeight()
    {
        if (!self::$carrier1 instanceof Carrier) {
            $carrier = new Carrier();
            $carrier->setName("Test-Carrier-Weight");
            $carrier->setLabel("Test-Carrier-Weight");
            $carrier->setGrade(1);
            $carrier->setMaxHeight(100);
            $carrier->setMaxWeight(100);
            $carrier->setMaxDepth(100);
            $carrier->setMaxWidth(100);
            $carrier->setShippingMethod(Carrier::SHIPPING_METHOD_WEIGHT);
            $carrier->setRangeBehaviour(Carrier::RANGE_BEHAVIOUR_DEACTIVATE);
            $carrier->setTaxRuleGroup(TaxRuleGroup::getById(1));
            $carrier->setIsFree(false);
            $carrier->setShopIds(array(Shop::getDefaultShop()->getId()));
            $carrier->save();

            $zoneCond = new Carrier\ShippingRule\Condition\Zones();
            $zoneCond->setZones([1]);

            $weightCond = new Carrier\ShippingRule\Condition\Weight();
            $weightCond->setMinWeight(0);
            $weightCond->setMaxWeight(5000);

            $dimensionCond = new Carrier\ShippingRule\Condition\Dimension();
            $dimensionCond->setHeight(100);
            $dimensionCond->setWidth(100);
            $dimensionCond->setDepth(100);

            $weightCond = new Carrier\ShippingRule\Condition\Weight();
            $weightCond->setMinWeight(0);
            $weightCond->setMaxWeight(100);

            $priceAct = new Carrier\ShippingRule\Action\FixedPrice();
            $priceAct->setFixedPrice(10);

            $rule1 = new Carrier\ShippingRule();
            $rule1->setName("carrier1-rule");
            $rule1->setActions([$priceAct]);
            $rule1->setConditions([$dimensionCond, $weightCond, $zoneCond, $weightCond]);
            $rule1->save();

            $ruleGroup = new Carrier\ShippingRuleGroup();
            $ruleGroup->setCarrier($carrier);
            $ruleGroup->setShippingRule($rule1);
            $ruleGroup->setPriority(1);
            $ruleGroup->save();

            self::$carrier1 = $carrier;
        }
    }

    public static function createTestCarrierPrice()
    {
        if (!self::$carrier2 instanceof Carrier) {
            $carrier = new Carrier();
            $carrier->setName("Test-Carrier-Weight No-Max");
            $carrier->setLabel("Test-Carrier-Weight No-Max");
            $carrier->setGrade(1);
            $carrier->setMaxHeight(0);
            $carrier->setMaxWeight(0);
            $carrier->setMaxDepth(0);
            $carrier->setMaxWidth(0);
            $carrier->setShippingMethod(Carrier::SHIPPING_METHOD_WEIGHT);
            $carrier->setRangeBehaviour(Carrier::RANGE_BEHAVIOUR_LARGEST);
            $carrier->setTaxRuleGroup(TaxRuleGroup::getById(1));
            $carrier->setIsFree(false);
            $carrier->setShopIds(array(Shop::getDefaultShop()->getId()));
            $carrier->save();

            $weightCond = new Carrier\ShippingRule\Condition\Weight();
            $weightCond->setMinWeight(0);
            $weightCond->setMaxWeight(5000);

            $zoneCond = new Carrier\ShippingRule\Condition\Zones();
            $zoneCond->setZones([1]);

            $priceAct = new Carrier\ShippingRule\Action\FixedPrice();
            $priceAct->setFixedPrice(20);

            $rule1 = new Carrier\ShippingRule();
            $rule1->setName("carrier2-rule");
            $rule1->setActions([$priceAct]);
            $rule1->setConditions([$zoneCond, $weightCond]);
            $rule1->save();

            $ruleGroup = new Carrier\ShippingRuleGroup();
            $ruleGroup->setCarrier($carrier);
            $ruleGroup->setShippingRule($rule1);
            $ruleGroup->setPriority(1);
            $ruleGroup->save();

            self::$carrier2 = $carrier;
        }
    }

    public static function createTestProduct()
    {
        if (!self::$product1 instanceof Product) {
            self::$product1 = Product::create();
            self::$product1->setName("test1");
            self::$product1->setWholesalePrice(10);
            self::$product1->setRetailPrice(15);
            self::$product1->setHeight(50);
            self::$product1->setWidth(50);
            self::$product1->setDepth(50);
            self::$product1->setWeight(50);
            self::$product1->setTaxRule(self::$taxRuleGroup);
            self::$product1->setParent(Service::createFolderByPath("/coreshop/products"));
            self::$product1->setKey(File::getValidFilename("test1"));
            self::$product1->setShops(array(Shop::getDefaultShop()->getId()));
            self::$product1->save();
        }

        if (!self::$product2 instanceof Product) {
            self::$product2 = Product::create();
            self::$product2->setName("test2");
            self::$product2->setWholesalePrice(100);
            self::$product2->setRetailPrice(150);
            self::$product2->setHeight(500);
            self::$product2->setWidth(500);
            self::$product2->setDepth(500);
            self::$product2->setWeight(500);
            self::$product1->setTaxRule(self::$taxRuleGroup);
            self::$product2->setParent(Service::createFolderByPath("/coreshop/products"));
            self::$product2->setKey(File::getValidFilename("test2"));
            self::$product2->setShops(array(Shop::getDefaultShop()->getId()));
            self::$product2->save();
        }

        if (!self::$product3 instanceof Product) {
            self::$product3 = Product::create();
            self::$product3->setName("test3");
            self::$product3->setWholesalePrice(50);
            self::$product3->setRetailPrice(75);
            self::$product3->setHeight(100);
            self::$product3->setWidth(100);
            self::$product3->setDepth(100);
            self::$product3->setWeight(100);
            self::$product1->setTaxRule(self::$taxRuleGroup);
            self::$product3->setParent(Service::createFolderByPath("/coreshop/products"));
            self::$product3->setKey(File::getValidFilename("test3"));
            self::$product3->setShops(array(Shop::getDefaultShop()->getId()));
            self::$product3->save();
        }
    }

    public static function createCart()
    {
        return Cart::prepare(true);
    }

    public static function createCartWithProducts()
    {
        $cart = self::createCart();

        $cart->addItem(self::$product1);
        $cart->addItem(self::$product2);
        $cart->addItem(self::$product3);

        return $cart;
    }

    public static function createCustomerGroups()
    {
        if (!self::$customerGroup1 instanceof Group) {
            self::$customerGroup1 = Group::create();
            self::$customerGroup1->setName("Group1");
            self::$customerGroup1->setShops(array(Shop::getDefaultShop()->getId()));
            self::$customerGroup1->setKey("group1");
            self::$customerGroup1->setParent(Service::createFolderByPath("/customer-groups"));
            self::$customerGroup1->save();
        }

        if (!self::$customerGroup2 instanceof Group) {
            self::$customerGroup2 = Group::create();
            self::$customerGroup2->setName("Group2");
            self::$customerGroup2->setShops(array(Shop::getDefaultShop()->getId()));
            self::$customerGroup2->setKey("group2");
            self::$customerGroup2->setParent(Service::createFolderByPath("/customer-groups"));
            self::$customerGroup2->save();
        }
    }

    public static function createCustomer()
    {
        if (!self::$customer1 instanceof User) {
            $customer = User::create();
            $customer->setKey("customer1");
            $customer->setParent(Service::createFolderByPath("/users"));
            $customer->setFirstname("customer");
            $customer->setLastname("1");
            $customer->setGender("m");
            $customer->setEmail("test@coreshop.org");
            $customer->setCustomerGroups(array(self::$customerGroup1));
            $customer->save();

            self::$customer1 = $customer;
        }
    }

    public static function createShop()
    {
        if (!self::$shop instanceof Shop) {
            $shop = new Shop();
            $shop->setName("test");
            $shop->setSiteId(1);
            $shop->setTemplate("default");
            $shop->setIsDefault(0);
            $shop->save();

            self::$shop = $shop;
        }
    }
}
