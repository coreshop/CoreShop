<?php

namespace CoreShop\Bundle\ResourceBundle\Form\Registry;

final class FormTypeRegistry implements FormTypeRegistryInterface
{
    /**
     * @var array
     */
    private $formTypes = [];

    /**
     * {@inheritdoc}
     */
    public function add($identifier, $typeIdentifier, $formType)
    {
        $this->formTypes[$identifier][$typeIdentifier] = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier, $typeIdentifier)
    {
        if (!$this->has($identifier, $typeIdentifier)) {
            return null;
        }

        return $this->formTypes[$identifier][$typeIdentifier];
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier, $typeIdentifier)
    {
        return isset($this->formTypes[$identifier][$typeIdentifier]);
    }
}
