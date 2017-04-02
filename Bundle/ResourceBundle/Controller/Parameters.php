<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\ParameterBag;

class Parameters extends ParameterBag
{
    /**
     * {@inheritdoc}
     */
    public function get($path, $default = null)
    {
        $result = parent::get($path, $default);

        if (null === $result && $default !== null && $this->has($path)) {
            $result = $default;
        }

        return $result;
    }
}
