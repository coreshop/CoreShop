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

declare(strict_types=1);

namespace CoreShop\Component\Notification\Processor;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class RulesProcessor implements RulesProcessorInterface
{
    private $ruleRepository;
    private $ruleValidationProcessor;
    private $ruleApplier;

    public function __construct(
        NotificationRuleRepositoryInterface $ruleRepository,
        RuleValidationProcessorInterface $ruleValidationProcessor,
        RuleApplierInterface $ruleApplier
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->ruleValidationProcessor = $ruleValidationProcessor;
        $this->ruleApplier = $ruleApplier;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRules(string $type, $subject, array $params = []): void
    {
        $rules = $this->ruleRepository->findForType($type);

        /**
         * @var NotificationRuleInterface $rule
         */
        foreach ($rules as $rule) {
            if ($this->ruleValidationProcessor->isValid($subject, $rule, ['params' => $params])) {
                $this->ruleApplier->applyRule($rule, $subject, $params);
            }
        }
    }
}
