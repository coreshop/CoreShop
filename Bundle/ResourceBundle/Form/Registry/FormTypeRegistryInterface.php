<?php

namespace CoreShop\Bundle\ResourceBundle\Form\Registry;

interface FormTypeRegistryInterface
{
    /**
     * @param string $identifier
     * @param string $typeIdentifier
     * @param string $formType
     */
    public function add($identifier, $typeIdentifier, $formType);

    /**
     * @param string $identifier
     * @param string $typeIdentifier
     *
     * @return string
     */
    public function get($identifier, $typeIdentifier);

    /**
     * @param string $identifier
     * @param string $typeIdentifier
     *
     * @return bool
     */
    public function has($identifier, $typeIdentifier);
}
