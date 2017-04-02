<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;

interface ResourceFormFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return FormInterface
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceInterface $resource);
}
