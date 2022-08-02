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
            ->getResult();
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = true')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult();
    }
}
