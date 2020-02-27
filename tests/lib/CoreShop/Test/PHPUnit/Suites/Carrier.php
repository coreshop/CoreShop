<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleActionType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleConditionType;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

class Carrier extends RuleTest
{
    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.shipping_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.shipping_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return ShippingRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.shipping_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.shipping_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return ShippingRuleActionType::class;
    }

    /**
     * @return TaxedShippingCalculatorInterface
     */
    protected function getPriceCalculator()
    {
        return $this->get('coreshop.carrier.price_calculator.taxed');
    }

    /**
     * @return ShippingRuleInterface
     */
    protected function createRule()
    {
        /**
         * @var ShippingRuleInterface
         */
        $shippingRule = $this->getFactory('shipping_rule')->createNew();
        $shippingRule->setName('test-rule');

        return $shippingRule;
    }

    /**
     * Test Carrier Creation.
     */
    public function testCarrierCreation()
    {
        $this->printTodoTestName();

        $carrier = $this->createResourceWithForm('carrier', CarrierInterface::class, [
            'label' => 'Test',
            'name' => 'Test',
            'translations' => [
                'en' => [
                    'description' => 'TEST'
                ]
            ]
        ]);

        $this->assertNull($carrier->getId());

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        $this->assertNotNull($carrier->getId());
    }

    /**
     * Test Carrier Price.
     */
    public function testCarrierPrice()
    {
        $this->printTestName();

        /**
         * @var $cart CartInterface
         */
        $cart = Data::createCartWithProducts();
        /**
         * @var $carrier CarrierInterface
         */
        $carrier = $this->createResourceWithForm('carrier', CarrierInterface::class, [
            'label' => 'Test',
            'name' => 'Test',
            'taxRule' => Data::$taxRuleGroup->getId(),
        ]);

        $this->getEntityManager()->persist($carrier);
        $this->getEntityManager()->flush();

        /**
         * @var $shippingRule ShippingRuleInterface
         */
        $shippingRule = $this->createResourceWithForm('shipping_rule', ShippingRuleInterface::class, [
            'name' => 'test->true',
            'active' => true
        ]);
        $shippingRule->addAction($this->createActionWithForm('price', [
            'price' => 10,
            'currency' => Data::$store->getCurrency()->getId(),
        ]));

        $this->getEntityManager()->persist($shippingRule);
        $this->getEntityManager()->flush();

        /**
         * @var $shippingRuleGroup ShippingRuleGroupInterface
         */
        $shippingRuleGroup = $this->createResourceWithForm('shipping_rule_group', ShippingRuleGroupInterface::class, [
            'carrier' => $carrier->getId(),
            'priority' => 1,
            'shippingRule' => $shippingRule->getId(),
        ]);

        $this->getEntityManager()->persist($shippingRuleGroup);
        $this->getEntityManager()->flush();

        $carrier->addShippingRule($shippingRuleGroup);

        $price = $this->getPriceCalculator()->getPrice($carrier, $cart, Data::$customer1->getAddresses()[0], false);
        $priceWithTax = $this->getPriceCalculator()->getPrice($carrier, $cart, Data::$customer1->getAddresses()[0], true);

        $this->assertEquals(1000, $price);
        $this->assertEquals(1200, $priceWithTax);
    }
}
