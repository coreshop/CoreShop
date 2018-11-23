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

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;

final class IndexObjectListener
{
    /**
     * @var IndexUpdaterServiceInterface
     */
    private $indexUpdaterService;

    /**
     * @var array
     */
    private $validObjectTypes = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT];

    /**
     * @param IndexUpdaterServiceInterface $indexUpdaterService
     */
    public function __construct(IndexUpdaterServiceInterface $indexUpdaterService)
    {
        $this->indexUpdaterService = $indexUpdaterService;
    }

    /**
     * @param ElementEventInterface $event
     */
    public function onPostUpdate(ElementEventInterface $event)
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof IndexableInterface) {
                return;
            }

            InheritanceHelper::useInheritedValues(function () use ($object) {
                $this->indexUpdaterService->updateIndices($object);
            });

            $classDefinition = ClassDefinition::getById($object->getClassId());
            if ($classDefinition->getAllowInherit() || $classDefinition->getAllowVariants()) {
                $this->updateInheritableChildren($object);
            }
        }
    }

    /**
     * @param AbstractObject $object
     */
    private function updateInheritableChildren(AbstractObject $object)
    {
        if (!$object->hasChildren($this->validObjectTypes)) {
            return;
        }

        $children = $object->getChildren($this->validObjectTypes);
        /** @var AbstractObject $child */
        foreach ($children as $child) {
            if (get_class($child) === get_class($object)) {
                InheritanceHelper::useInheritedValues(function () use ($child) {
                    $this->indexUpdaterService->updateIndices($child);
                });
                $this->updateInheritableChildren($child);
            }
        }
    }

    /**
     * @param ElementEventInterface $event
     */
    public function onPostDelete(ElementEventInterface $event)
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof IndexableInterface) {
                return;
            }

            InheritanceHelper::useInheritedValues(function () use ($object) {
                $this->indexUpdaterService->removeIndices($object);
            });
        }
    }
}
