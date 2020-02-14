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

namespace CoreShop\Bundle\ConfigurationBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface;

class ConfigurationRepository extends EntityRepository implements ConfigurationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByKey(string $key): ?ConfigurationInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
