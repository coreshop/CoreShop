<?php

class CoreShop_Base extends Object_Concrete
{
    public function toArray()
    {
        return CoreShop_Tool::objectToArray($this);
    }
}
