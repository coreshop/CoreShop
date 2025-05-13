<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.com/license     GNU General Public License version 3 (GPLv3)
 */

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

namespace CoreShop\Component\Resource\DataHub;

use Doctrine\ORM\Mapping\ClassMetadata;

class GraphHydrator
{
    public $em;

    public function __construct(
        $em,
    ) {
        $this->em = $em;
    }

    public function hydrate(?array $data, string $entityType): ?GraphEntity
    {
        if ($data === null) {
            return null;
        }

        // Get the doctrine type
        $class = $this->em->getClassMetadata($entityType);
        $discriminatorColumn = null;

        if (count($class->subClasses) > 0) {
            $discriminatorColumn = $class->discriminatorColumn['name'];
            $className = $class->discriminatorMap[$data[$discriminatorColumn]];

            $instanceType = $this->em->getClassMetadata($className);
        } else {
            $instanceType = $class;
        }

        $entity = $this->newInstance($instanceType, $this->getId($instanceType, $data));

        if (method_exists($entity, '__init')) {
            $entity->__init();
        }

        foreach ($data as $field => $value) {
            if (isset($instanceType->fieldMappings[$field])) {
                $instanceType->reflFields[$field]->setValue($entity, $value);
            }
        }

        $this->registerManaged($instanceType, $entity, $data);

        return new GraphEntity($data, $entity);
    }

    private function newInstance(ClassMetadata $class, array $id)
    {
        $entity = $this->em->getProxyFactory()->getProxy($class->name, $id);

        if ($entity instanceof \Doctrine\Common\Persistence\ObjectManagerAware) {
            $entity->injectObjectManager($this->em, $class);
        }

        return $entity;
    }

    protected function getId(ClassMetadata $class, array $data): array
    {
        // Generate the unique id
        if ($class->isIdentifierComposite) {
            $id = [];

            foreach ($class->identifier as $fieldName) {
                $id[$fieldName] = isset($class->associationMappings[$fieldName])
                    ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']]
                    : $data[$fieldName];
            }
        } else {
            $fieldName = $class->identifier[0];
            $id = [
                $fieldName => isset($class->associationMappings[$fieldName])
                    ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']]
                    : $data[$fieldName],
            ];
        }

        return $id;
    }

    protected function registerManaged($class, $entity, array $data)
    {
        $id = $this->getId($class, $data);
        $this->em->getUnitOfWork()->registerManaged($entity, $id, $data);
    }
}
