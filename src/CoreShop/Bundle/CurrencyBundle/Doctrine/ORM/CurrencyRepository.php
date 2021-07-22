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
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\ORM\QueryBuilder;

class CurrencyRepository extends EntityRepository implements CurrencyRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.countries', 'c')
            ->andWhere('c.active = true')
            ->getQuery()
            ->getResult();
    }

    public function getByCode(string $currencyCode): ?CurrencyInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isoCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
