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
    private array $validObjectTypes = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT];

    public function __construct(private IndexUpdaterServiceInterface $indexUpdaterService)
    {
    }

    public function onPostUpdate(ElementEventInterface $event): void
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof IndexableInterface) {
                return;
            }

            $isVersionEvent = $event->hasArgument('saveVersionOnly') && true === $event->getArgument('saveVersionOnly');

            InheritanceHelper::useInheritedValues(function () use ($object, $isVersionEvent) {
                $this->indexUpdaterService->updateIndices($object, $isVersionEvent);
            });

            $classDefinition = ClassDefinition::getById($object->getClassId());
            if ($classDefinition && ($classDefinition->getAllowInherit() || $classDefinition->getAllowVariants())) {
                $this->updateInheritableChildren($object, $isVersionEvent);
            }
        }
    }

    private function updateInheritableChildren(AbstractObject $object, bool $isVersionChange): void
    {
        if (!$object->hasChildren($this->validObjectTypes)) {
            return;
        }

        $children = $object->getChildren($this->validObjectTypes);
        /** @var AbstractObject $child */
        foreach ($children as $child) {
            if ($child instanceof IndexableInterface && $child::class === $object::class) {
                InheritanceHelper::useInheritedValues(function () use ($child, $isVersionChange) {
                    $this->indexUpdaterService->updateIndices($child, $isVersionChange);
                });
                $this->updateInheritableChildren($child, $isVersionChange);
            }
        }
    }

    public function onPostDelete(ElementEventInterface $event): void
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
