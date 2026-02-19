<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

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
            ->getResult()
        ;
    }
}
