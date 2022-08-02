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

declare(strict_types=1);

namespace CoreShop\Bundle\RuleBundle\Collector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class RuleCollector extends DataCollector
{
    public function __construct(private array $validationProcessors)
    {
    }

    public function getProcessedConditions(): array
    {
        return $this->data['processedConditions'];
    }

    public function getProcessedRules(): array
    {
        return $this->data['processedRules'];
    }

    public function getTypes(): array
    {
        return $this->data['processedTypes'];
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $processedConditions = [];
        $processedRules = [];
        $processedTypes = [];

        foreach ($this->validationProcessors as $validationProcessor) {
            $processedSubjects = $validationProcessor->getValidatedConditions();

            $processedConditions[$validationProcessor->getType()] = $processedSubjects;

            foreach ($processedSubjects as $subject) {
                foreach ($subject['rules'] as $rule) {
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

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'coreshop.rule_collector';
    }
}
