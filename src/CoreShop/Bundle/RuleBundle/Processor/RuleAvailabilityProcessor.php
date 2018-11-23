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

namespace CoreShop\Bundle\RuleBundle\Processor;

use CoreShop\Bundle\RuleBundle\Event\RuleAvailabilityCheckEvent;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Rule\Condition\Assessor\RuleAvailabilityAssessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RuleAvailabilityProcessor implements RuleAvailabilityProcessorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ServiceRegistryInterface
     */
    private $ruleRegistry;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface   $entityManager
     * @param ServiceRegistryInterface $ruleRegistry
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        ServiceRegistryInterface $ruleRegistry
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->ruleRegistry = $ruleRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
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

    /**
     * @param RuleInterface $rule
     * @param bool          $ruleIsAvailable
     */
    private function processRule(RuleInterface $rule, bool $ruleIsAvailable)
    {
        /** @var RuleAvailabilityCheckEvent $event */
        $event = $this->eventDispatcher->dispatch(
            'coreshop.rule.availability_check',
            new RuleAvailabilityCheckEvent($rule, get_class($rule), $ruleIsAvailable)
        );

        if ($event->isAvailable() === false) {
            if ($rule instanceof ToggleableInterface) {
                $rule->setActive(false);
                $this->entityManager->persist($rule);
            }
        }
    }
}
