<?php

namespace CoreShop\Bundle\FixtureBundle\Repository;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class DataFixtureRepository extends EntityRepository implements DataFixtureRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByClassName($className)
    {
        return $this->findBy(['className' => $className]);
    }

    /**
     * {@inheritdoc}
     */
    public function isDataFixtureExists($where, array $parameters = [])
    {
        $entityId = $this->createQueryBuilder('m')
            ->select('m.id')
            ->where($where)
            ->setMaxResults(1)
            ->getQuery()
            ->execute($parameters);

        return $entityId ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateDataFixtureHistory(array $updateFields, $where, array $parameters = [])
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->update($this->getEntityName(), 'm')
            ->where($where);

        foreach ($updateFields as $fieldName => $fieldValue) {
            $qb->set($fieldName, $fieldValue);
        }
        $qb->getQuery()->execute($parameters);
    }
}
