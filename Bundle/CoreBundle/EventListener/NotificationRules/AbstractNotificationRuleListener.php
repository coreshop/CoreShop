<?php

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Notification\Processor\RulesProcessorInterface;

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
