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

namespace CoreShop\Bundle\PaymentBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface;

class PaymentProviderRepository extends EntityRepository implements PaymentProviderRepositoryInterface
{
    public function findByTitle(string $title, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.title = :title')
            ->andWhere('translation.locale = :locale')
            ->setParameter('title', $title)
            ->setParameter('locale', $locale)
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = true')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }
}
