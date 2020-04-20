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

namespace CoreShop\Bundle\SEOBundle\EventListener;

use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Http\RequestHelper;
use Pimcore\Templating\Helper\HeadMeta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSEOListener implements EventSubscriberInterface
{
    protected $seoPresentation;
    protected $requestHelper;
    protected $pimcoreContextResolver;

    public function __construct(
        SEOPresentationInterface $seoPresentation,
        RequestHelper $requestHelper,
        PimcoreContextResolver $contextResolver
    ) {
        $this->seoPresentation = $seoPresentation;
        $this->requestHelper = $requestHelper;
        $this->pimcoreContextResolver = $contextResolver;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -255],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($event->isMasterRequest() === false) {
            return;
        }

        if ($this->pimcoreContextResolver->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            return;
        }

        if (!$this->pimcoreContextResolver->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        if ($this->requestHelper->isFrontendRequestByAdmin($request)) {
            return;
        }

        if (php_sapi_name() === 'cli') {
            return;
        }

        $this->seoPresentation->updateSeoMetadata($request);
    }
}
