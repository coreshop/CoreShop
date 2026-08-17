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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use Pimcore\Bundle\AdminBundle\Event\BundleManagerEvents;
use Pimcore\Event\BundleManager\PathsEvent;
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
