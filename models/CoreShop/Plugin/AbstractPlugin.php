<?php
    
namespace CoreShop\Plugin;
    
interface AbstractPlugin
{
    public function getName();
    public function getImage();
    public function getDescription();
    public function getIdentifier();
}