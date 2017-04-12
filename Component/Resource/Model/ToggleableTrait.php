<?php

namespace CoreShop\Component\Resource\Model;

trait ToggleableTrait
{
    /**
     * @var bool
     */
    protected $active = true;

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $enabled
     */
    public function setActive($enabled)
    {
        $this->active = (bool) $enabled;
    }

    public function activate()
    {
        $this->active = true;
    }

    public function disable()
    {
        $this->active = false;
    }
}
