<?php

namespace CoreShop\Bundle\ResourceBundle\Repository;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

class PimcoreRepository implements PimcoreRepositoryInterface
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $className = $this->metadata->getClass('model');

        return $className::getList();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $className = $this->metadata->getClass('model');

        return $className::getById($id);
    }
}
