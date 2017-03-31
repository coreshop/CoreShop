<?php

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver;

use CoreShop\Component\Core\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface DriverInterface
{
    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata);

    /**
     * Returns unique name of the driver.
     *
     * @return string
     */
    public function getType();
}
