<?php

namespace CoreShop\Component\Address\Context\RequestBased;

use Symfony\Component\HttpFoundation\Request;

final class ConfigurationBasedResolver implements RequestResolverInterface
{

    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        //TODO:
    }
}
