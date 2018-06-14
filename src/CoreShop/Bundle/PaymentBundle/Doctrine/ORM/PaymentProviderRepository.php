<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PaymentBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface;

class PaymentProviderRepository extends EntityRepository implements PaymentProviderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByTitle($title, $locale)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.title = :title')
            ->andWhere('translation.locale = :locale')
            ->setParameter('title', $title)
            ->setParameter('locale', $locale)
            ->addOrderBy('o.position')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name, $locale)
    {
        @trigger_error('findByName is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use findByTitle instead', E_USER_DEPRECATED);

        return $this->findByTitle($name, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = true')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult();
    }
}
