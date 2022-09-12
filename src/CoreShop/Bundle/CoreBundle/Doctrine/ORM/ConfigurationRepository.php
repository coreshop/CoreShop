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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ConfigurationBundle\Doctrine\ORM\ConfigurationRepository as BaseConfigurationRepository;
use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Component\Core\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ConfigurationRepository extends BaseConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function findForKeyAndStore(string $key, StoreInterface $store): ?ConfigurationInterface
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
