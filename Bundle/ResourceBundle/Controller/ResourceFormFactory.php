<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceFormFactory implements ResourceFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $formType = $requestConfiguration->getFormType();
        $formOptions = $requestConfiguration->getFormOptions();

        if ($requestConfiguration->isHtmlRequest()) {
            return $this->formFactory->create($formType, $resource, $formOptions);
        }

        return $this->formFactory->createNamed('', $formType, $resource, array_merge($formOptions, ['csrf_protection' => false]));
    }
}
