<?php
    
namespace CoreShop\Interface;
    
interface Plugin
{
    public function getName();
    public function getImage();
    public function getDescription();
    public function getIdentifier();
}