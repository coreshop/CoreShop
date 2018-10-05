<?php

namespace CoreShop\Bundle\FixtureBundle\Repository;

use CoreShop\Bundle\FixtureBundle\Model\DataFixture;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityRepository;

interface DataFixtureRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $className
     *
     * @return DataFixture[]
     */
    public function findByClassName($className);
    /**
     * @param string $where
     * @param array $parameters
     *
     * @return bool
     */
    public function isDataFixtureExists($where, array $parameters = []);

    /**
     * Update data fixture history
     *
     * @param array $updateFields assoc array with field names and values that should be updated
     * @param string $where condition
     * @param array $parameters optional parameters for where condition
     */
    public function updateDataFixtureHistory(array $updateFields, $where, array $parameters = []);
}
