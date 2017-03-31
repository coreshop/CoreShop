<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Exception;

class InvalidDriverException extends \Exception
{
    public function __construct($driver, $className)
    {
        parent::__construct(sprintf(
            'Driver "%s" is not supported by %s.',
            $driver,
            $className
        ));
    }
}
