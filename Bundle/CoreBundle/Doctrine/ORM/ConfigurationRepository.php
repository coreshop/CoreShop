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

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ConfigurationBundle\Doctrine\ORM\ConfigurationRepository as BaseConfigurationRepository;
use CoreShop\Component\Core\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ConfigurationRepository extends BaseConfigurationRepository implements ConfigurationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForKeyAndStore($key, StoreInterface $store)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.key = :configKey')
            ->andWhere('o.store = :store')
            ->setParameter('configKey', $key)
            ->setParameter('store', $store)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
