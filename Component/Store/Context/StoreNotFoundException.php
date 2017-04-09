<?php

namespace CoreShop\Component\Store\Context;

class StoreNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Exception $previousException = null)
    {
        parent::__construct('Store could not be found!', 0, $previousException);
    }
}
