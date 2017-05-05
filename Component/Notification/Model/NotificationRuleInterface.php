<?php

namespace CoreShop\Component\Notification\Model;

use CoreShop\Component\Rule\Model\RuleInterface;

interface NotificationRuleInterface extends RuleInterface
{
    /**
     * @return bool
     */
    public function getActive();

    /**
     * @param bool $active
     *
     * @return static
     */
    public function setActive($active);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);
}
