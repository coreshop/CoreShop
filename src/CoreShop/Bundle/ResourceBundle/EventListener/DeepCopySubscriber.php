<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use DeepCopy\DeepCopy;
use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Matcher\PropertyTypeMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DeepCopySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        //TODO: Change to Pimcore\Event\SystemEvents::SERVICE_PRE_GET_DEEP_COPY later
        //TODO: Event has been added with 6.8.5, min requirement is 6.6 for now.
        return [
            'pimcore.system.service.preGetDeepCopy' => 'addDoctrineCollectionFilter',
        ];
    }

    public function addDoctrineCollectionFilter(GenericEvent $event)
    {
        $context = $event->getArgument('context');

        //Only add if not already been added
        if (!($context['defaultFilters'] ?? false)) {
            /**
             * @var DeepCopy $copier
             */
            $copier = $event->getArgument('copier');
            $copier->addFilter(new DoctrineCollectionFilter(),
                new PropertyTypeMatcher('Doctrine\Common\Collections\Collection'));
            $event->setArgument('copier', $copier);
        }
    }
}
