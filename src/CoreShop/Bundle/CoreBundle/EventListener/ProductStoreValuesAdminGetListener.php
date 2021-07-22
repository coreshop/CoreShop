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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\ProductInterface;
use Pimcore\Event\AdminEvents;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductStoreValuesAdminGetListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::OBJECT_GET_PRE_SEND_DATA => 'prepareData',
        ];
    }

    public function prepareData(GenericEvent $event)
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

        foreach ($data['data']['storeValues'] as $storeId => &$storeValues) {
            $values = $storeValues['values'] ?? [];

            if (!isset($values['product'])) {
                continue;
            }

            if ($values['product'] !== $object->getId()) {
                $storeValues['inherited'] = true;
            }
        }

        unset ($storeValues);

        $event->setArgument('data', $data);
    }
}
