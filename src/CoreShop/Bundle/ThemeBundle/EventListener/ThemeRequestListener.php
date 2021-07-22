<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ThemeBundle\EventListener;

use CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ThemeRequestListener implements EventSubscriberInterface
{
    /**
     * @var ThemeResolverInterface
     */
    private $themeResolver;

    /**
     * @var ActiveThemeInterface
     */
    private $activeTheme;

    /**
     * @param ThemeResolverInterface $themeResolver
     * @param ActiveThemeInterface   $activeTheme
     */
    public function __construct(ThemeResolverInterface $themeResolver, ActiveThemeInterface $activeTheme)
    {
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
