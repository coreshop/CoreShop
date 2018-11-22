<?php

namespace CoreShop\Bundle\FixtureBundle\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\SetValuesTrait;

class DataFixture extends AbstractResource implements DataFixtureInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var \DateTime
     */
    protected $loadedAt;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadedAt()
    {
        return $this->loadedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setLoadedAt($loadedAt)
    {
        $this->loadedAt = $loadedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }
}
