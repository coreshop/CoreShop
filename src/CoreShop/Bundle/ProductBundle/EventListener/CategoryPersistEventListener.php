<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ProductBundle\EventListener;

use CoreShop\Component\Product\Model\CategoryInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\ElementEventInterface;

final class CategoryPersistEventListener
{
    public function onPreUpdate(ElementEventInterface $event): void
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof CategoryInterface) {
                return;
            }

            $parent = $object->getParent();

            if ($parent instanceof CategoryInterface) {
                $object->setParentCategory($parent);
            } else {
                $object->setParentCategory(null);
            }
        }
    }
}
