<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;

class CartPriceRuleVoucherRepository extends EntityRepository implements CartPriceRuleVoucherRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByCode($code)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countCodes(int $length, ?string $prefix = null, ?string $suffix = null): int
    {
        if ($prefix !== null) {
            $length += strlen($prefix);
        }

        if ($suffix !== null) {
            $length += strlen($suffix);
        }

        $code = $prefix . '%' . $suffix;

        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('LENGTH(o.code) = :length')
            ->andWhere('o.code LIKE :code')
            ->setParameter('length', $length)
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
