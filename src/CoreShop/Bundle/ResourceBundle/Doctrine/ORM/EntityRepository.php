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

namespace CoreShop\Bundle\ResourceBundle\Doctrine\ORM;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;

class EntityRepository extends BaseEntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(ResourceInterface $resource)
    {
        $this->_em->persist($resource);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource)
    {
        if (null !== $this->find($resource->getId())) {
            $this->_em->remove($resource);
            $this->_em->flush();
        }
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->createQueryBuilder('o')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return 'o' . '.' . $name;
        }

        return $name;
    }
}
