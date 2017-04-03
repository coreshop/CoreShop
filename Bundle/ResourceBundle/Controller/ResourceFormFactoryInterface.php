<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;

interface ResourceFormFactoryInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     *
     * @return FormInterface
     */
    public function create(MetadataInterface $metadata, ResourceInterface $resource);
}
