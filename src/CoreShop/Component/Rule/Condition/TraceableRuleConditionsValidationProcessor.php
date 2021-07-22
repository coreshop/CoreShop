<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class TraceableRuleConditionsValidationProcessor implements TraceableRuleConditionsValidationProcessorInterface
{
    /**
     * @var RuleConditionsValidationProcessorInterface
     */
    private $ruleConditionsValidationProcessor;

    /**
     * @var array
     */
    private $processed;

    /**
     * @param RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor
     */
    public function __construct(RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor)
    {
        $this->ruleConditionsValidationProcessor = $ruleConditionsValidationProcessor;

        $this->processed = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->ruleConditionsValidationProcessor->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, $conditions, $params = [])
    {
        if (!$rule->getActive()) {
            return false;
        }

        if (!count($conditions)) {
            $this->addProcessedRule($subject, $rule, true);

            return true;
        }
        $ruleResult = true;

        foreach ($conditions as $condition) {
            $conditionResult = $this->isConditionValid($subject, $rule, $condition, $params);

            if (!$conditionResult) {
                $ruleResult = false;
            }

            $this->addProcessedRule($subject, $rule, $ruleResult, $condition, $conditionResult);
        }

        return $ruleResult;
    }

    /**
     * {@inheritdoc}
     */
    public function isConditionValid(ResourceInterface $subject, RuleInterface $rule, ConditionInterface $condition, $params = [])
    {
        $isValid = $this->ruleConditionsValidationProcessor->isConditionValid($subject, $rule, $condition, $params);

        $this->addProcessedRule($subject, $rule, $isValid, $condition);

        return $isValid;
    }

    /**
     * @param ResourceInterface       $subject
     * @param RuleInterface           $rule
     * @param bool                    $ruleResult
     * @param ConditionInterface|null $condition
     * @param bool                    $conditionResult
     */
    protected function addProcessedRule(ResourceInterface $subject, RuleInterface $rule, $ruleResult = false, ConditionInterface $condition = null, $conditionResult = false)
    {
        if (!isset($this->processed[$subject->getId()])) {
            $this->processed[$subject->getId()] = [
                'subject' => $subject,
                'type' => get_class($subject),
                'rules' => [],
            ];
        }

        if (!isset($this->processed[$subject->getId()]['rules'][$rule->getId()])) {
            $actions = [];

            foreach ($rule->getActions() as $action) {
                $actions[$action->getId()] = [
                    'action' => $action,
                    'configuration' => $action->getConfiguration(),
                    'type' => $action->getType(),
                ];
            }

            $this->processed[$subject->getId()]['rules'][$rule->getId()] = [
                'rule' => $rule,
                'result' => $ruleResult,
                'conditions' => [],
                'actions' => $actions,
            ];
        }

        if (null !== $condition) {
            $this->processed[$subject->getId()]['rules'][$rule->getId()]['result'] = $ruleResult;
            $this->processed[$subject->getId()]['rules'][$rule->getId()]['conditions'][$condition->getId()] = [
                'condition' => $condition,
                'configuration' => $condition->getConfiguration(),
                'type' => $condition->getType(),
                'result' => $conditionResult,
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatedConditions()
    {
        return $this->processed;
    }
}
