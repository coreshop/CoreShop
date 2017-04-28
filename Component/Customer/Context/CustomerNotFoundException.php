<?php

namespace CoreShop\Component\Customer\Context;

class CustomerNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Exception $previousException = null)
    {
        parent::__construct('Customer could not be found!', 0, $previousException);
    }
}
