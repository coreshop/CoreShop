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

namespace CoreShop\Bundle\PimcoreBundle\AdminClass\EventListener;

use Pimcore\Bundle\AdminBundle\Event\BundleManagerEvents;
use Pimcore\Event\BundleManager\PathsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminJavascriptListener implements EventSubscriberInterface
{
    public function __construct(
        private array $jsResources,
        private array $editmodeJsResources,
        private array $cssResources,
        private array $editmodeCssResources,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            /** @phpstan-ignore-next-line */
            BundleManagerEvents::JS_PATHS => 'getAdminJavascript',
            /** @phpstan-ignore-next-line */
            BundleManagerEvents::CSS_PATHS => 'getAdminCss',
            /** @phpstan-ignore-next-line */
            BundleManagerEvents::EDITMODE_JS_PATHS => 'getEditmodeAdminJavascript',
            /** @phpstan-ignore-next-line */
            BundleManagerEvents::EDITMODE_CSS_PATHS => 'getEditmodeAdminCSS',
        ];
    }

    public function getAdminJavascript(PathsEvent $event): void
    {
        if (count($this->jsResources) === 0) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->jsResources));
    }

    public function getAdminCss(PathsEvent $event): void
    {
        if (count($this->cssResources) === 0) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->cssResources));
    }

    public function getEditmodeAdminJavascript(PathsEvent $event): void
    {
        if (count($this->editmodeJsResources) === 0) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->editmodeJsResources));
    }

    public function getEditmodeAdminCSS(PathsEvent $event): void
    {
        if (count($this->editmodeCssResources) === 0) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->editmodeCssResources));
    }
}
