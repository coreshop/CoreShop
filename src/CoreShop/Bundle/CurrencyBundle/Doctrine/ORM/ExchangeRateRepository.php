<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CurrencyBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Currency\Repository\ExchangeRateRepositoryInterface;

class ExchangeRateRepository extends EntityRepository implements ExchangeRateRepositoryInterface
{
    public function findOneWithCurrencyPair(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency): ?ExchangeRateInterface
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->andWhere($expr->orX(
                'o.fromCurrency = :firstCurrency AND o.toCurrency = :secondCurrency',
                'o.toCurrency = :firstCurrency AND o.fromCurrency = :secondCurrency',
            ))
            ->setParameter('firstCurrency', $fromCurrency)
            ->setParameter('secondCurrency', $toCurrency)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
