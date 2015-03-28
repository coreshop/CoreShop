<?php

namespace CoreShop\Interface;

interface User
{
    public static function getUniqueByEmail($email);
    
    public function authenticate($password);
}