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

namespace CoreShop\Bundle\ResourceBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Model\DataObject;

class StackRepository extends PimcoreRepository implements StackRepositoryInterface
{
    private array $classNames = [];

    public function __construct(
        MetadataInterface $metadata,
        Connection $connection,
        private string $interface,
        private array $fqnStackClasses,
    ) {
        parent::__construct($metadata, $connection);

        foreach ($fqnStackClasses as $class) {
            $namespaces = explode('\\', $class);

            $this->classNames[] = '"' . end($namespaces) . '"';
        }
    }

    public function getClassIds(): array
    {
        $ids = [];

        foreach ($this->fqnStackClasses as $stackClass) {
            $ids[] = $stackClass::classId();
        }

        return $ids;
    }

    public function findAll(): array
    {
        $list = $this->getList();

        return $list->getObjects();
    }

    public function getList()
    {
        $list = new DataObject\Listing();
        $list->addConditionParam(sprintf('className IN (%s)', implode(',', $this->classNames)));

        return $list;
    }

    public function forceFind($id, bool $force = true)
    {
        $instance = DataObject::getById((int) $id, ['force' => $force]);

        if (null === $instance) {
            return null;
        }

        if (!in_array($this->interface, class_implements($instance) ?: [], true)) {
            return null;
        }

        return $instance;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
    {
        $criteria['variable'] = implode(',', $this->classNames);

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria)
    {
        $instance = parent::findOneBy($criteria);

        if (!in_array($this->interface, class_implements($instance) ?: [], true)) {
            return null;
        }

        return $instance;
    }
}
