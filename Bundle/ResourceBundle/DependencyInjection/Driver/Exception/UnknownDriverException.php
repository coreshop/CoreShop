<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Exception;

class UnknownDriverException extends \Exception
{
    public function __construct($driver)
    {
        parent::__construct(sprintf(
            'Unknown driver "%s".',
            $driver
        ));
    }
}
