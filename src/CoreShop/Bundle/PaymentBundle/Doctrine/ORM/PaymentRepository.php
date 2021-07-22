<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PaymentBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;

class PaymentRepository extends EntityRepository implements PaymentRepositoryInterface
{
    public function findForPayable(PayableInterface $payable): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.orderId = :orderId')
            ->setParameter('orderId', $payable->getId())
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
