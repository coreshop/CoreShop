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

use CoreShop\Bundle\IndexBundle\Messenger\IndexDeleteMessage;
use CoreShop\Component\Index\Model\IndexableInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\Messenger\MessageBusInterface;

final class IndexDeleteObjectListener
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function onPreDelete(ElementEventInterface $event): void
    {
        if (!$event instanceof DataObjectEvent) {
            return;
        }

        $object = $event->getObject();

        if (!$object instanceof IndexableInterface) {
            return;
        }

        if (!$object instanceof DataObject\Concrete) {
            return;
        }

        $this->recurisveDeleteMessage($object);
    }

    private function recurisveDeleteMessage(DataObject\Concrete $object)
    {
        $this->messageBus->dispatch(new IndexDeleteMessage($object->getClassName(), $object->getId()));
        $objectTypes = [DataObject\AbstractObject::OBJECT_TYPE_OBJECT, DataObject\AbstractObject::OBJECT_TYPE_VARIANT];

        foreach ($object->getChildren($objectTypes, true) as $child) {
            if (!$child instanceof DataObject\Concrete) {
                continue;
            }

            if (!$object instanceof IndexableInterface) {
                continue;
            }

            $this->recurisveDeleteMessage($child);
        }
    }
}
