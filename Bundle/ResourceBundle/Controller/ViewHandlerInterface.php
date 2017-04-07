<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

interface ViewHandlerInterface
{
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function handle($data, $options = []);
}
