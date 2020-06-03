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

declare(strict_types=1);

namespace CoreShop\Bundle\PimcoreBundle\EventListener;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Pimcore\Tool\Admin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

final class AdminJavascriptListener implements EventSubscriberInterface
{
    private $router;
    private $jsResources;
    private $editmodeJsResources;
    private $cssResources;
    private $editmodeCssResources;

    public function __construct(
        RouterInterface $router,
        array $jsResources,
        array $editmodeJsResources,
        array $cssResources,
        array $editmodeCssResources
    )
    {
        $this->router = $router;
        $this->jsResources = $jsResources;
        $this->editmodeJsResources = $editmodeJsResources;
        $this->cssResources = $cssResources;
        $this->editmodeCssResources = $editmodeCssResources;
    }

    public static function getSubscribedEvents()
    {
        return [
            BundleManagerEvents::JS_PATHS => 'getAdminJavascript',
            BundleManagerEvents::CSS_PATHS => 'getAdminCss',
            BundleManagerEvents::EDITMODE_JS_PATHS => 'getEditmodeAdminJavascript',
            BundleManagerEvents::EDITMODE_CSS_PATHS => 'getEditmodeAdminCSS',
        ];
    }

    public function getAdminJavascript(PathsEvent $event)
    {
        if (count($this->jsResources) === 0) {
            return;
        }

        if (\Pimcore::getDevMode()) {
            $event->setPaths(array_merge($event->getPaths(), $this->jsResources));
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), [
            $this->prepareResources($this->jsResources)
        ]));
    }

    public function getAdminCss(PathsEvent $event)
    {
        if (count($this->cssResources) === 0) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->cssResources));
    }

    public function getEditmodeAdminJavascript(PathsEvent $event)
    {
        if (count($this->editmodeJsResources) === 0) {
            return;
        }

        if (\Pimcore::getDevMode()) {
            $event->setPaths(array_merge($event->getPaths(), $this->editmodeJsResources));
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), [
            $this->prepareResources($this->editmodeJsResources)
        ]));
    }

    public function getEditmodeAdminCSS(PathsEvent $event)
    {
        if (count($this->editmodeCssResources) === 0) {
            return;
        }

        $event->setPaths(array_merge($event->getPaths(), $this->editmodeCssResources));
    }

    private function prepareResources(array $resources)
    {
        $scriptContents = '';

        foreach ($resources as $scriptUrl) {
            if (is_file(PIMCORE_WEB_ROOT . $scriptUrl)) {
                $scriptContents .= file_get_contents(PIMCORE_WEB_ROOT . $scriptUrl) . "\n\n\n";
            }
        }

        return $this->router->generate('pimcore_admin_misc_scriptproxy', Admin::getMinimizedScriptPath($scriptContents, false));
    }
}
