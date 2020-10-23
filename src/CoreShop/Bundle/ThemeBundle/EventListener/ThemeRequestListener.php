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

namespace CoreShop\Bundle\ThemeBundle\EventListener;

use CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ThemeRequestListener implements EventSubscriberInterface
{
    private $pimcoreContext;
    private $themeResolver;
    private $activeTheme;

    public function __construct(PimcoreContextResolver $pimcoreContextResolver, ThemeResolverInterface $themeResolver, ActiveThemeInterface $activeTheme)
    {
        $this->pimcoreContext = $pimcoreContextResolver;
        $this->themeResolver = $themeResolver;
        $this->activeTheme = $activeTheme;
    }

    public static function getSubscribedEvents()
    {
        return [
            // priority must be after
            // -> Pimcore\Bundle\CoreBundle\EventListener\Frontend\DocumentFallbackListener
            KernelEvents::REQUEST => ['onKernelRequest', 19],
            KernelEvents::CONTROLLER => ['onKernelController', 19],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->resolveTheme($event);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $this->resolveTheme($event);
    }

    protected function resolveTheme(KernelEvent $event)
    {
        if ($this->pimcoreContext->matchesPimcoreContext($event->getRequest(), PimcoreContextResolver::CONTEXT_ADMIN)) {
            return;
        }

        if (!$event->isMasterRequest()) {
            $exception = $event->getRequest()->get('exception', null);

            if (empty($exception)) {
                return;
            }
        }

        try {
            $this->themeResolver->resolveTheme($this->activeTheme);
        } catch (ThemeNotResolvedException $exception) {
        }
    }
}
