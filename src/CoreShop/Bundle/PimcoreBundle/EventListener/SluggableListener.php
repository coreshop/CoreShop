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

namespace CoreShop\Bundle\PimcoreBundle\EventListener;

use CoreShop\Component\Pimcore\Slug\DataObjectSlugGeneratorInterface;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SluggableListener implements EventSubscriberInterface
{
    public function __construct(
        protected DataObjectSlugGeneratorInterface $generator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_UPDATE => 'preUpdate',
            DataObjectEvents::PRE_ADD => 'preUpdate',
        ];
    }

    public function preUpdate(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof SluggableInterface) {
            return;
        }

        $this->generator->generateSlugs($object);
    }
}
