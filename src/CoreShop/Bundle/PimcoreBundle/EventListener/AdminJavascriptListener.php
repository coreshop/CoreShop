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
            BundleManagerEvents::JS_PATHS => 'getAdminJavascript',
            BundleManagerEvents::CSS_PATHS => 'getAdminCss',
            BundleManagerEvents::EDITMODE_JS_PATHS => 'getEditmodeAdminJavascript',
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
