<?php

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\Element\ElementInterface;

class ObjectIdSumInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexColumnInterface $config = null)
    {
        $sum = 0;
        if (is_array($value)) {
            foreach ($value as $object) {
                if ($object instanceof ElementInterface) {
                    $sum += $object->getId();
                }
            }
        }
        return $sum;
    }
}
