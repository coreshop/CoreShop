<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PimcoreBundle\EventListener;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminJavascriptListener implements EventSubscriberInterface
{
    public function __construct(private array $jsResources, private array $editmodeJsResources, private array $cssResources, private array $editmodeCssResources)
    {
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
        if (0 === count($this->jsResources)) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->jsResources));
    }

    public function getAdminCss(PathsEvent $event): void
    {
        if (0 === count($this->cssResources)) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->cssResources));
    }

    public function getEditmodeAdminJavascript(PathsEvent $event): void
    {
        if (0 === count($this->editmodeJsResources)) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->editmodeJsResources));
    }

    public function getEditmodeAdminCSS(PathsEvent $event): void
    {
        if (0 === count($this->editmodeCssResources)) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->editmodeCssResources));
    }
}
