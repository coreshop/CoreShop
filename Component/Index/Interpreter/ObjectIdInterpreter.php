<?php

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\Object\AbstractObject;

class ObjectIdInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexColumnInterface $config = null)
    {
        if ($value instanceof AbstractObject) {
            return $value->getId();
        }

        return null;
    }
}
