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
 *
*/

namespace CoreShop\Bundle\ShippingBundle\Checker;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class CarrierShippingRuleChecker implements CarrierShippingRuleCheckerInterface
{
    /**
     * @var RuleValidationProcessorInterface
     */
    protected $ruleValidationProcessor;

    /**
     * @param RuleValidationProcessorInterface $ruleValidationProcessor
     */
    public function __construct(RuleValidationProcessorInterface $ruleValidationProcessor)
    {
        $this->ruleValidationProcessor = $ruleValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address)
    {
        $shippingRules = $carrier->getShippingRules();

        foreach ($shippingRules as $rule) {
            if ($this->ruleValidationProcessor->isValid(["carrier" => $carrier, "cart" => $cart, "address" => $address], $rule->getShippingRule())) {
                return $rule;
            }
        }

        return false;
    }
}