<?php

namespace CoreShop\Component\Rule\Condition;

interface ConditionCheckerInterface
{
    /**
     * @param $subject
     * @param array $configuration
     *
     * @return bool
     */
    public function isValid($subject, array $configuration);
}
