<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\RuleBundle\Processor;

use CoreShop\Bundle\RuleBundle\Event\RuleAvailabilityCheckEvent;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Condition\Assessor\RuleAvailabilityAssessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RuleAvailabilityProcessor implements RuleAvailabilityProcessorInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $entityManager,
        private ServiceRegistryInterface $ruleRegistry,
    ) {
    }

    public function process(): void
    {
        /** @var RuleAvailabilityAssessorInterface $ruleAssessor */
        foreach ($this->ruleRegistry->all() as $ruleAssessor) {
            foreach ($ruleAssessor->getRules() as $rule) {
                $ruleIsAvailable = $ruleAssessor->isValid($rule);
                $this->processRule($rule, $ruleIsAvailable);
            }
        }

        $this->entityManager->flush();
    }

    private function processRule(RuleInterface $rule, bool $ruleIsAvailable): void
    {
        /** @var RuleAvailabilityCheckEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new RuleAvailabilityCheckEvent($rule, $rule::class, $ruleIsAvailable),
            'coreshop.rule.availability_check',
        );

        if ($event->isAvailable() === false) {
            $rule->setActive(false);
            $this->entityManager->persist($rule);
        }
    }
}
