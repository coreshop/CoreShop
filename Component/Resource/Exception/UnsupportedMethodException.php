<?php

namespace CoreShop\Component\Resource\Exception;

class UnsupportedMethodException extends \Exception
{
    /**
     * @param string $methodName
     */
    public function __construct($methodName)
    {
        parent::__construct(sprintf(
            'The method "%s" is not supported.',
            $methodName
        ));
    }
}
