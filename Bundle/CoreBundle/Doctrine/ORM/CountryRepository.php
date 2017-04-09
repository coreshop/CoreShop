<?php

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Bundle\AddressBundle\Doctrine\ORM\CountryRepository as BaseCountryRepository;
use CoreShop\Component\Store\Model\StoreInterface;

class CountryRepository extends BaseCountryRepository implements CountryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForStore(StoreInterface $store)
    {
        //TODO
        return $this->find(1);
    }
}