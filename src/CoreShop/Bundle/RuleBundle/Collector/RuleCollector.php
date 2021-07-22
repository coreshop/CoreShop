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

namespace CoreShop\Bundle\RuleBundle\Collector;

use CoreShop\Component\Rule\Condition\TraceableRuleConditionsValidationProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class RuleCollector extends DataCollector
{
    /**
     * @var TraceableRuleConditionsValidationProcessorInterface[]
     */
    private $validationProcessors;

    /**
     * @param TraceableRuleConditionsValidationProcessorInterface[] $validationProcessors
     */
    public function __construct(array $validationProcessors)
    {
        $this->validationProcessors = $validationProcessors;
    }

    /**
     * @return array
     */
    public function getProcessedConditions()
    {
        return $this->data['processedConditions'];
    }

    /**
     * @return array
     */
    public function getProcessedRules()
    {
        return $this->data['processedRules'];
    }

    /**
     * @return mixed
     */
    public function getTypes()
    {
        return $this->data['processedTypes'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $processedConditions = [];
        $processedRules = [];
        $processedTypes = [];

        foreach ($this->validationProcessors as $validationProcessor) {
            $processedSubjects = $validationProcessor->getValidatedConditions();

            $processedConditions[$validationProcessor->getType()] = $processedSubjects;

            foreach ($processedSubjects as $subject) {
                foreach ($subject['rules'] as $ruleId => $rule) {
                    $processedRules[] = $rule['rule'];
                }
            }

            $processedTypes[] = [
                'name' => $validationProcessor->getType(),
                'count' => count($processedConditions[$validationProcessor->getType()]),
            ];
        }

        $this->data['processedTypes'] = $processedTypes;
        $this->data['processedRules'] = $processedRules;
        $this->data['processedConditions'] = $processedConditions;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop.rule_collector';
    }
}
