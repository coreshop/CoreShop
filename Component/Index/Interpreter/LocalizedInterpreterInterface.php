<?php

namespace CoreShop\Component\Index\Interpreter;

interface LocalizedInterpreterInterface extends InterpreterInterface {

    /**
     * @param $language
     * @param $value
     * @param null $config
     * @return mixed
     */
    public function interpretForLanguage($language, $value, $config = null);
}