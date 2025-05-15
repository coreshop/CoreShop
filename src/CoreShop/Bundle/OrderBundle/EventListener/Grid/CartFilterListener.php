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

namespace CoreShop\Bundle\OrderBundle\EventListener\Grid;

use Pimcore\Model\DataObject;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CartFilterListener
{
    public function checkObjectList(GenericEvent $event): void
    {
        $list = $event->getArgument('list');
        $context = $event->getArgument('context');

        if (!isset($context['coreshop_cart'])) {
            return;
        }

        if ($list instanceof DataObject\Listing) {
            $list->addConditionParam('customer__id IS NOT NULL');

            $event->setArgument('list', $list);
        }
    }
}
