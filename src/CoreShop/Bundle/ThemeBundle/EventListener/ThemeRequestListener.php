<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ThemeBundle\EventListener;

use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class ThemeRequestListener
{
    /**
     * @var ThemeResolverInterface
     */
    private $themeResolver;

    /**
     * @param ThemeResolverInterface $themeResolver
     */
    public function __construct(ThemeResolverInterface $themeResolver)
    {
        $this->themeResolver = $themeResolver;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {

            $exception = $event->getRequest()->get('exception', null);

            if (empty($exception)) {
                return;
            }
        }

        $this->themeResolver->resolveTheme();
    }
}