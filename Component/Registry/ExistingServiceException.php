<?php

namespace CoreShop\Component\Registry;

class ExistingServiceException extends \InvalidArgumentException
{
    public function __construct($context, $type)
    {
        parent::__construct(sprintf('%s of type "%s" already exists.', $context, $type));
    }
}
