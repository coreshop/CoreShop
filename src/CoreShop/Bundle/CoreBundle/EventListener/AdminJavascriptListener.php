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

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

final class AdminJavascriptListener implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::JS_PATHS => 'getAdminJavascript',
        ];
    }

    public function getAdminJavascript(PathsEvent $event): void
    {
        $event->setPaths(array_merge($event->getPaths(), [
            $this->router->generate('coreshop_menu', ['type' => 'coreshop.main']),
        ]));
    }
}
