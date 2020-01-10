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

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class RelationsHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'getSingleEntityRelation'], $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = isset($type['params'][0]['name']) ? $type['params'][0]['name'] : null;

        $metadata = $this->manager->getClassMetadata($className);

        if (!is_array($relation)) {
            return $this->manager->getReference($metadata->getName(), $relation);
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->manager->getReference($className, $relation);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->manager->getReference($className, $idSet);
        }

        return $objects;
    }

    /**
     * @param mixed $relation
     *
     * @return array|mixed
     */
    protected function getSingleEntityRelation($relation)
    {
        $metadata = $this->manager->getClassMetadata(get_class($relation));

        $ids = $metadata->getIdentifierValues($relation);
        if (!$metadata->isIdentifierComposite) {
            $ids = array_shift($ids);
        }

        return $ids;
    }
}
