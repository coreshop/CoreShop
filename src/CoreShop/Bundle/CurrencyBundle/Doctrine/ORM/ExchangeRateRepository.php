<?php

namespace CoreShop\Bundle\CurrencyBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\ExchangeRateRepositoryInterface;

class ExchangeRateRepository extends EntityRepository implements ExchangeRateRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneWithCurrencyPair(CurrencyInterface $firstCurrency, CurrencyInterface $secondCurrency)
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->andWhere($expr->orX(
                'fromCurrency = :firstCurrency AND toCurrency.code = :secondCurrency',
                'toCurrency.code = :firstCurrency AND fromCurrency.code = :secondCurrency'
            ))
            ->setParameter('firstCurrency', $firstCurrency)
            ->setParameter('secondCurrency', $secondCurrency)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
