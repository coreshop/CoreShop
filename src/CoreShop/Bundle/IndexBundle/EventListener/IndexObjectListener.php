<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
    private IndexUpdaterServiceInterface $indexUpdaterService;
    private array $validObjectTypes = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT];

    public function __construct(IndexUpdaterServiceInterface $indexUpdaterService)
    {
        $this->indexUpdaterService = $indexUpdaterService;
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
            if (get_class($child) === get_class($object)) {
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
