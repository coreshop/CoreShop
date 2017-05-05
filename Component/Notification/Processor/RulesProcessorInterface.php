<?php

namespace CoreShop\Component\Notification\Processor;

interface RulesProcessorInterface
{
    /**
     * @param $type
     * @param $subject
     * @param array $params
     * @return mixed
     */
    public function applyRules($type, $subject, $params = []);
}