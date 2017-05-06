<?php

namespace CoreShop\Component\Index\Interpreter;

class Soundex implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret($value, $config = null)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            sort($value);
            $string = implode(' ', $value);
        } else {
            $string = (string) $value;
        }

        $soundEx = soundex($string);

        return intval(ord(substr($soundEx, 0, 1)).substr($soundEx, 1));
    }
}
