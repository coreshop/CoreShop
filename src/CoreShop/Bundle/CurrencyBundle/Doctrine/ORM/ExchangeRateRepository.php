<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CurrencyBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Currency\Repository\ExchangeRateRepositoryInterface;

class ExchangeRateRepository extends EntityRepository implements ExchangeRateRepositoryInterface
{
    public function findOneWithCurrencyPair(CurrencyInterface $firstCurrency, CurrencyInterface $secondCurrency): ?ExchangeRateInterface
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->andWhere($expr->orX(
                'o.fromCurrency = :firstCurrency AND o.toCurrency = :secondCurrency',
                'o.toCurrency = :firstCurrency AND o.fromCurrency = :secondCurrency'
            ))
            ->setParameter('firstCurrency', $firstCurrency)
            ->setParameter('secondCurrency', $secondCurrency)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
