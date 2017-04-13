<?php

namespace CoreShop\Component\Sequence\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface SequenceRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $type
     * @return mixed
     */
    public function findForType($type);

}