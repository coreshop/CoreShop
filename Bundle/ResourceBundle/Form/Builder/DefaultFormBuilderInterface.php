<?php

namespace CoreShop\Bundle\ResourceBundle\Form\Builder;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface DefaultFormBuilderInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param FormBuilderInterface $formBuilder
     * @param array $options
     */
    public function build(MetadataInterface $metadata, FormBuilderInterface $formBuilder, array $options);
}
