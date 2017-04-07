<?php

namespace CoreShop\Component\Index\Interpreter;

interface InterpreterInterface
{
    /**
     * @param $value
     * @param null $config
     *
     * @return mixed
     */
    public function interpret($value, $config = null);
}
