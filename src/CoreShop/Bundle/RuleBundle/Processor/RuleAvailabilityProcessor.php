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

namespace CoreShop\Bundle\RuleBundle\Processor;

use CoreShop\Bundle\RuleBundle\Event\RuleAvailabilityCheckEvent;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Rule\Condition\Assessor\RuleAvailabilityAssessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RuleAvailabilityProcessor implements RuleAvailabilityProcessorInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private EntityManagerInterface $entityManager, private ServiceRegistryInterface $ruleRegistry)
    {
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
            'coreshop.rule.availability_check'
        );

        if ($event->isAvailable() === false) {
            $rule->setActive(false);
            $this->entityManager->persist($rule);
        }
    }
}
