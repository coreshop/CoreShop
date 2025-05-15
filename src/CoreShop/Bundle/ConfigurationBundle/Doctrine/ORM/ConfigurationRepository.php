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

namespace CoreShop\Bundle\ConfigurationBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface;

class ConfigurationRepository extends EntityRepository implements ConfigurationRepositoryInterface
{
    public function findByKey(string $key): ?ConfigurationInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
