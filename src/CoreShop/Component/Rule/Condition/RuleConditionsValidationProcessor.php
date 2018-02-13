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

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleConditionsValidationProcessor implements RuleConditionsValidationProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $ruleRegistry;

    /**
     * @var string
     */
    private $type;

    /**
     * @param ServiceRegistryInterface $ruleRegistry
     */
    public function __construct(ServiceRegistryInterface $ruleRegistry, $type)
    {
        $this->ruleRegistry = $ruleRegistry;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, $conditions, $params = [])
    {
        if (!count($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (!$this->isConditionValid($subject, $rule, $condition, $params)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isConditionValid(ResourceInterface $subject, RuleInterface $rule, ConditionInterface $condition, $params = [])
    {
        /** @var ConditionCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($condition->getType());

        return $checker->isValid($subject, $rule, $condition->getConfiguration(), $params);
    }
}
