<?php

namespace CoreShop\Bundle\TrackingBundle\Model;

class PromotionData extends AbstractData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $creative;

    /**
     * @var string
     */
    public $position;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCreative()
    {
        return $this->creative;
    }

    /**
     * @param string $creative
     */
    public function setCreative($creative)
    {
        $this->creative = $creative;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
