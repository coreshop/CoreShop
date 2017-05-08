<?php

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\Object\AbstractObject;

class ObjectPropertyInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexColumnInterface $config = null)
    {
        $config = isset($config) ? $config->getInterpreterConfig() : [];

        if ($value instanceof AbstractObject) {
            if (array_key_exists("property", $config)) {
                $name = $config['property'];
                $getter = "get" . ucfirst($name);

                if (method_exists($value, $getter)) {
                    return $value->$getter();
                }
            }
        }

        return null;
    }
}
