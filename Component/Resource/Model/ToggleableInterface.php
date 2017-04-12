<?php

namespace CoreShop\Component\Resource\Model;

interface ToggleableInterface
{
    /**
     * @return bool
     */
    public function getActive();

    /**
     * @param bool $active
     */
    public function setActive($active);

    public function activate();

    public function disable();
}
