<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestConfigurationFactoryInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param Request $request
     *
     * @return RequestConfiguration
     *
     * @throws \InvalidArgumentException
     */
    public function create(MetadataInterface $metadata, Request $request);
}
