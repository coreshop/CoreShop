<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Model\TaxRuleInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Calculator\TaxRulesTaxCalculator;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Test\Base;

class Workflow extends Base
{
    public function testWorkflowInstallation()
    {
        $this->printTodoTestName();
    }

    public function testWorkflowValidators()
    {
        $this->printTodoTestName();
    }
}
