<?php

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface CustomerGroupInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return static
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getShops();

    /**
     * @param $shops
     * @return mixed
     */
    public function setShops($shops);
}