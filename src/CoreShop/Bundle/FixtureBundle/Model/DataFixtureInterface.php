<?php

namespace CoreShop\Bundle\FixtureBundle\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface DataFixtureInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getClassName();

    /**
     * @param string $className
     */
    public function setClassName($className);

    /**
     * @return \DateTime
     */
    public function getLoadedAt();

    /**
     * @param \DateTime $loadedAt
     */
    public function setLoadedAt($loadedAt);

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getVersion();
}
