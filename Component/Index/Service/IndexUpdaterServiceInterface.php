<?php

namespace CoreShop\Component\Index\Service;

interface IndexUpdaterServiceInterface
{
    /**
     * Update all Indicies with $subject
     *
     * @param $subject
     */
    public function updateIndices($subject);
}