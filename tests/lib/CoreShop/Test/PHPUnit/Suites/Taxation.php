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

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Calculator\TaxRulesTaxCalculator;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Test\Base;

class Taxation extends Base
{
    /**
     * Test Tax Creation.
     */
    public function testTaxRateCreation()
    {
        $this->printTestName();

        /**
         * @var TaxRateInterface
         */
        $taxRate = $this->getFactory('tax_rate')->createNew();
        $taxRate->setRate(20);
        $taxRate->setActive(true);

        $this->getEntityManager()->persist($taxRate);
        $this->getEntityManager()->flush();

        $this->assertNotNull($taxRate->getId());

        $this->getEntityManager()->remove($taxRate);
        $this->getEntityManager()->flush();

        $this->assertNull($taxRate->getId());
    }

    /**
     * Test TaxRule Creation.
     */
    public function testTaxRuleCreation()
    {
        $this->printTestName();

        /**
         * @var TaxRuleGroupInterface
         */
        $taxRuleGroup = $this->getFactory('tax_rule_group')->createNew();
        $taxRuleGroup->setName('test');

        /**
         * @var TaxRateInterface
         */
        $taxRate = $this->getFactory('tax_rate')->createNew();
        $taxRate->setRate(10);
        $taxRate->setName('AT10', 'de');

        /**
         * @var TaxRuleInterface
         */
        $taxRule = $this->getFactory('tax_rule')->createNew();
        $taxRule->setBehavior(TaxCalculatorInterface::COMBINE_METHOD);
        $taxRule->setTaxRuleGroup($taxRuleGroup);
        $taxRule->setTaxRate($taxRate);

        $this->getEntityManager()->persist($taxRate);
        $this->getEntityManager()->persist($taxRuleGroup);
        $this->getEntityManager()->persist($taxRule);
        $this->getEntityManager()->flush();

        $this->assertNotNull($taxRule->getId());

        $this->getEntityManager()->remove($taxRule);
        $this->getEntityManager()->flush();

        $this->assertNull($taxRule->getId());
    }

    /**
     * Test TaxRule Creation.
     */
    public function testTaxRuleGroupCreation()
    {
        $this->printTestName();

        /**
         * @var TaxRuleGroupInterface
         */
        $taxRuleGroup = $this->getFactory('tax_rule_group')->createNew();
        $taxRuleGroup->setName('test');

        $this->getEntityManager()->persist($taxRuleGroup);
        $this->getEntityManager()->flush();

        $this->assertNotNull($taxRuleGroup->getId());

        $this->getEntityManager()->remove($taxRuleGroup);
        $this->getEntityManager()->flush();

        $this->assertNull($taxRuleGroup->getId());
    }

    /**
     * Test Tax Calculator.
     */
    public function testTaxCalculator()
    {
        $this->printTestName();

        /**
         * @var TaxRateInterface
         * @var $tax20           TaxRateInterface
         */
        $tax10 = $this->getFactory('tax_rate')->createNew();
        $tax10->setRate(10);

        $tax20 = $this->getFactory('tax_rate')->createNew();
        $tax20->setRate(20);

        /**
         * @var TaxCalculatorInterface
         */
        $taxCalculator = new TaxRulesTaxCalculator([$tax10], TaxCalculatorInterface::DISABLE_METHOD);

        $this->assertEquals(1100, $taxCalculator->applyTaxes(1000));
        $this->assertEquals(1320, $taxCalculator->applyTaxes(1200));

        $taxCalculator = new TaxRulesTaxCalculator([$tax10, $tax20], TaxCalculatorInterface::COMBINE_METHOD);

        $this->assertEquals(1300, $taxCalculator->applyTaxes(1000));
        $this->assertEquals(1560, $taxCalculator->applyTaxes(1200));

        $taxCalculator = new TaxRulesTaxCalculator([$tax10, $tax20], TaxCalculatorInterface::ONE_AFTER_ANOTHER_METHOD);

        $this->assertEquals(1320, $taxCalculator->applyTaxes(1000));
        $this->assertEquals(1584, $taxCalculator->applyTaxes(1200));
    }

    public function testTaxCalculatorFactoryService()
    {
        $this->assertInstanceOf(TaxCalculatorFactoryInterface::class, $this->get('coreshop.taxation.factory.tax_calculator'));
    }
}
