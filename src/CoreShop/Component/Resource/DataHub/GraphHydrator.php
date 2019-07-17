<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\DataHub;

use Doctrine\ORM\EntityManagerInterface;

class GraphHydrator
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function hydrate($data, $entityType)
    {
        if ($data === null) {
            return null;
        }

        $class = $this->em->getClassMetadata($entityType);

        $discriminatorColumn = null;
        $instanceType = $class;

        if (count($class->subClasses) > 0) {
            $discriminatorColumn = $class->discriminatorColumn['name'];
            $className = $class->discriminatorMap[$data[$discriminatorColumn]];

            $instanceType = $this->em->getClassMetadata($className);
        }

        $entity = $this->newInstance($instanceType, $this->getId($instanceType, $data));

        if (method_exists($entity, '__init')) {
            $entity->__init();
        }

        // Populate the fields with the data
        foreach ($data as $field => $value) {
            if (isset($instanceType->fieldMappings[$field])) {
                $instanceType->reflFields[$field]->setValue($entity, $value);
            }
        }

        $this->registerManaged($instanceType, $entity, $data);

        return new GraphEntity($data, $entity);
    }

    private function newInstance($class, $id)
    {
        $entity = $this->em->getProxyFactory()->getProxy($class->name, $id);

        //$entity = $class->newInstance();

        if ($entity instanceof \Doctrine\Common\Persistence\ObjectManagerAware) {
            $entity->injectObjectManager($this->em, $class);
        }

        return $entity;

    }

    protected function getId($class, $data)
    {
        // Generate the unique id
        if ($class->isIdentifierComposite) {
            $id = array();

            foreach ($class->identifier as $fieldName) {
                $id[$fieldName] = isset($class->associationMappings[$fieldName])
                    ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']]
                    : $data[$fieldName];
            }
        } else {
            $fieldName = $class->identifier[0];
            $id = array(
                $fieldName => isset($class->associationMappings[$fieldName])
                    ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']]
                    : $data[$fieldName],
            );
        }

        return $id;
    }

    protected function registerManaged($class, $entity, array $data)
    {
        $id = $this->getId($class, $data);
        $this->em->getUnitOfWork()->registerManaged($entity, $id, $data);
    }
}
