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

namespace CoreShop\Bundle\RuleBundle\Collector;

use CoreShop\Component\Rule\Condition\TraceableRuleConditionsValidationProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class RuleCollector extends DataCollector
{
    private $validationProcessors;

    public function __construct(array $validationProcessors)
    {
        $this->validationProcessors = $validationProcessors;
    }

    /**
     * @return array
     */
    public function getProcessedConditions(): array
    {
        return $this->data['processedConditions'];
    }

    /**
     * @return array
     */
    public function getProcessedRules(): array
    {
        return $this->data['processedRules'];
    }

    /**
     * @return mixed
     */
    public function getTypes(): array
    {
        return $this->data['processedTypes'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
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
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'coreshop.rule_collector';
    }
}
