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

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use DeepCopy\DeepCopy;
use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Matcher\PropertyTypeMatcher;
use Pimcore\Event\SystemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DeepCopySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::SERVICE_PRE_GET_DEEP_COPY => 'addDoctrineCollectionFilter',
        ];
    }

    public function addDoctrineCollectionFilter(GenericEvent $event): void
    {
        $context = $event->getArgument('context');

        //Only add if not already been added
        if (!($context['defaultFilters'] ?? false)) {
            /**
             * @var DeepCopy $copier
             */
            $copier = $event->getArgument('copier');
            $copier->addFilter(
                new DoctrineCollectionFilter(),
                new PropertyTypeMatcher('Doctrine\Common\Collections\Collection'),
            );
            $event->setArgument('copier', $copier);
        }
    }
}
