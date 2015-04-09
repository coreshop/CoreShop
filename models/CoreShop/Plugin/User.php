<?php

namespace CoreShop\Plugin;

interface User
{
    public static function getUniqueByEmail($email);
    
    public function authenticate($password);
    public function findAddressByName($name);
}