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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\NotificationBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;

class NotificationRuleEventListener
{
    public function __construct(
        private NotificationRuleRepositoryInterface $repository,
    ) {
    }

    public function preCreate(ResourceControllerEvent $event): void
    {
        $object = $event->getSubject();

        if (!$object instanceof NotificationRuleInterface) {
            return;
        }

        $object->setSort(count($this->repository->findAll()) + 1);
    }
}
