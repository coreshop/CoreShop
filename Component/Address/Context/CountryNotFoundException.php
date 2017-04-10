<?php

namespace CoreShop\Component\Address\Context;

class CountryNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Exception $previousException = null)
    {
        parent::__construct('Country could not be found!', 0, $previousException);
    }
}
