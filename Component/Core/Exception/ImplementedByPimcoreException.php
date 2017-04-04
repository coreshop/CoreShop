<?php

namespace CoreShop\Component\Core;

class ImplementedByPimcoreException extends \InvalidArgumentException
{
    public function __construct($class, $property)
    {
        parent::__construct(sprintf('%s of "%s" needs to be implemented by Pimcore.', $class, $property));
    }
}
