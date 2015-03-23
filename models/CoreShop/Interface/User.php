<?php
    
interface CoreShop_Interface_User
{
    public static function getUniqueByEmail($email);
    
    public function authenticate($password);
}