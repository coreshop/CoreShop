<?php

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface CartPriceRuleVoucherRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $code
     * @return mixed
     */
    public function findByCode($code);

}