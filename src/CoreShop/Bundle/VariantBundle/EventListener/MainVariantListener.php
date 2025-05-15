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

namespace CoreShop\Bundle\VariantBundle\EventListener;

use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MainVariantListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_UPDATE => 'preUpdate',
            DataObjectEvents::POST_UPDATE => 'postUpdate',
            DataObjectEvents::POST_ADD => 'postUpdate',
        ];
    }

    public function preUpdate(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof ProductVariantAwareInterface) {
            return;
        }

        if ($object->getType() !== AbstractObject::OBJECT_TYPE_OBJECT) {
            return;
        }

        $variant = $object->findMainVariant();

        if (!$variant instanceof Concrete) {
            return;
        }

        $object->setMainVariant($variant);
    }

    public function postUpdate(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof ProductVariantAwareInterface) {
            return;
        }

        if (!$object->getPublished()) {
            return;
        }

        if ($object->getType() !== AbstractObject::OBJECT_TYPE_VARIANT) {
            return;
        }

        $product = $object->getVariantParent();
        $product->save();
    }
}
