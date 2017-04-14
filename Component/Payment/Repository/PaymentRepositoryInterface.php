<?php

namespace CoreShop\Component\Payment\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param integer $orderId
     * @return mixed
     */
    public function findForOrderId($orderId);

}