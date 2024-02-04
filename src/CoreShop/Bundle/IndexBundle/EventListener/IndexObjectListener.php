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

use CoreShop\Bundle\IndexBundle\Messenger\IndexMessage;
use CoreShop\Component\Index\Model\IndexableInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\ElementEventInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class IndexObjectListener
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function onPostUpdate(ElementEventInterface $event): void
    {
        if (!$event instanceof DataObjectEvent) {
            return;
        }

        $object = $event->getObject();

        if (!$object instanceof IndexableInterface) {
            return;
        }

        $isVersionEvent = $event->hasArgument('saveVersionOnly') && true === $event->getArgument('saveVersionOnly');

        $this->messageBus->dispatch(new IndexMessage($object->getId(), $isVersionEvent));
    }
}
