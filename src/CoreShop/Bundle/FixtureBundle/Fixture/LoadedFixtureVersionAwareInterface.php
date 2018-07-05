<?php

namespace CoreShop\Bundle\FixtureBundle\Fixture;

interface LoadedFixtureVersionAwareInterface
{
    /**
     * Set current loaded fixture version.
     *
     * @param $version
     */
    public function setLoadedVersion($version = null);
}
