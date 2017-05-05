<?php

namespace CoreShop\Component\Notification\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface NotificationRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $type
     * @return mixed
     */
    public function findForType($type);
}