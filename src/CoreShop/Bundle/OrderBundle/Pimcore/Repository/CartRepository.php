<?php

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Repository\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Repository\CartRepositoryInterface;

class CartRepository extends PimcoreRepository implements CartRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForCustomer(CustomerInterface $customer)
    {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND order__id is null', [$customer->getId()]);
        $list->load();

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findNamedForCustomer(CustomerInterface $customer, $name)
    {
        $list = $this->getList();
        $list->setCondition('user__id = ? AND name = ? AND order__id is null', [$customer->getId(), $name]);
        $list->load();

        if ($list->getTotalCount() > 0) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }
}