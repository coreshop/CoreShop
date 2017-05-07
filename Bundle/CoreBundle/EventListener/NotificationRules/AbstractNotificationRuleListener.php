<?php

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Notification\Processor\RulesProcessorInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractNotificationRuleListener
{
    /**
     * @var RulesProcessorInterface
     */
    protected $rulesProcessor;

    /**
     * @param RulesProcessorInterface $rulesProcessor
     */
    public function __construct(RulesProcessorInterface $rulesProcessor)
    {
        $this->rulesProcessor = $rulesProcessor;
    }
}
