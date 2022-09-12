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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\ProductInterface;
use Pimcore\Event\AdminEvents;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductStoreValuesAdminGetListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AdminEvents::OBJECT_GET_PRE_SEND_DATA => 'prepareData',
        ];
    }

    public function prepareData(GenericEvent $event): void
    {
        $object = $event->getArgument('object');
        if (!$object instanceof ProductInterface) {
            return;
        }

        $classDefinition = ClassDefinition::getById($object->getClassId());

        if (!$classDefinition) {
            return;
        }

        //No inheritance enabled, no need to check then
        if (!$classDefinition->getAllowInherit()) {
            return;
        }

        //Since the parent is not set, not data will be inherited anyway
        if (!$object->getParent() instanceof $object) {
            return;
        }

        $data = $event->getArgument('data');

        foreach ($data['data']['storeValues'] as &$storeValues) {
            $values = $storeValues['values'] ?? [];

            if (!isset($values['product'])) {
                continue;
            }

            if ($values['product'] !== $object->getId()) {
                $storeValues['inherited'] = true;
            }
        }

        unset($storeValues);

        $event->setArgument('data', $data);
    }
}
