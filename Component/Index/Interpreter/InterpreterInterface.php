<?php

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexColumnInterface;

interface InterpreterInterface
{
    /**
     * @param $value
     * @param IndexColumnInterface $config
     *
     * @return mixed
     */
    public function interpret($value, IndexColumnInterface $config = null);
}
