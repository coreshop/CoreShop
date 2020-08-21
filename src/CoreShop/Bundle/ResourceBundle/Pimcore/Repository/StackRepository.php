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

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Listing;

class StackRepository extends PimcoreRepository
{
    private $classNames = [];
    private $fqnStackClasses = [];
    private $interface;

    public function __construct(MetadataInterface $metadata, Connection $connection, $interface, array $stackClasses)
    {
        parent::__construct($metadata, $connection);

        $this->interface = $interface;
        $this->fqnStackClasses = $stackClasses;

        foreach ($stackClasses as $class) {
            $namespaces = explode('\\', $class);

            $this->classNames[] = '"'.end($namespaces).'"';
        }
    }

    /**
     * @return array
     */
    public function getClassIds()
    {
        $ids = [];

        foreach ($this->fqnStackClasses as $stackClass) {
            $ids[] = $stackClass::classId();
        }

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $list = $this->getList();

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(): Listing
    {
        $list = new DataObject\Listing();
        $list->addConditionParam(sprintf('o_className IN (%s)', implode(',', $this->classNames)));

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function forceFind($id, bool $force = true): ?DataObject\Concrete
    {
        $instance = DataObject::getById($id, $force);

        if (!$instance instanceof DataObject\Concrete) {
            return null;
        }

        if (!in_array($this->interface, class_implements($instance), true)) {
            return null;
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $criteria[] = [
            'variable' => implode(',', $this->classNames),
        ];

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        $instance = parent::findOneBy($criteria);

        if (!in_array($this->interface, class_implements($instance), true)) {
            return null;
        }

        return $instance;
    }
}
