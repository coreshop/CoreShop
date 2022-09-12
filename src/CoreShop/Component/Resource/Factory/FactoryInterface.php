<?php
declare(strict_types=1);

namespace CoreShop\Component\Resource\Factory;

interface FactoryInterface
{
    /**
     * @return mixed
     */
    public function createNew();
}
