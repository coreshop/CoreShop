<?php

namespace CoreShop\Bundle\TrackingBundle\Model;

class AbstractData
{
    /**
     * @var string
     */
    public $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
