<?php

namespace CoreShop\Component\Index\Condition;

interface RendererInterface
{
    /**
     * Renders the condition
     *
     * @param ConditionInterface $condition
     * @return mixed
     */
    public function render(ConditionInterface $condition);
}
