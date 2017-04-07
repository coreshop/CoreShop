<?php

namespace CoreShop\Component\Index\Condition;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * Renders the condition.
     *
     * @param ConditionInterface $condition
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function render(ConditionInterface $condition)
    {
        $type = ucfirst($condition->getType());

        $functionName = 'render'.$type;

        if (method_exists($this, $functionName)) {
            return $this->$functionName($condition);
        }

        throw new \Exception(sprintf('No render function for type %s found', $condition->getType()));
    }
}
