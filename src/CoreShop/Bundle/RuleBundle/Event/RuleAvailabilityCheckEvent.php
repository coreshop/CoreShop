<?php

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Component\EventDispatcher\Event;

final class RuleAvailabilityCheckEvent extends Event
{
    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @var string
     */
    private $ruleType;

    /**
     * @var bool
     */
    private $available;

    /**
     * RuleAvailabilityCheckEvent constructor.
     *
     * @param RuleInterface $rule
     * @param string        $ruleType
     * @param bool          $available
     */
    public function __construct(RuleInterface $rule, string $ruleType, bool $available)
    {
        $this->rule = $rule;
        $this->ruleType = $ruleType;
        $this->available = $available;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return string
     */
    public function getRuleType()
    {
        return $this->ruleType;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailability(bool $available)
    {
        $this->available = $available;
    }
}