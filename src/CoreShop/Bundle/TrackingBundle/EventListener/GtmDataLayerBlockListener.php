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

namespace CoreShop\Bundle\TrackingBundle\EventListener;

use CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager\CodeTracker;
use Pimcore\Bundle\GoogleMarketingBundle\SiteId\SiteIdProvider;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Http\ResponseHelper;
use Pimcore\Tool;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class GtmDataLayerBlockListener
{
    public function __construct(
        protected PimcoreContextResolver $pimcoreContextResolver,
        protected ResponseHelper $responseHelper,
        protected SiteIdProvider $siteIdProvider,
        protected CodeTracker $codeTracker,
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->pimcoreContextResolver->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        /**
         * @psalm-suppress InternalMethod
         */
        if (!Tool::useFrontendOutputFilters()) {
            return;
        }

        $serverVars = $event->getRequest()->server;
        if ($serverVars->get('HTTP_X_PURPOSE') === 'preview') {
            return;
        }

        $response = $event->getResponse();
        /**
         * @psalm-suppress InternalMethod
         */
        if (!$this->responseHelper->isHtmlResponse($response)) {
            return;
        }

        $codeHead = $this->generateCode();
        $content = $response->getContent();

        if (false === $content) {
            return;
        }

        if (!empty($codeHead)) {
            $headEndPosition = stripos($content, '</head>');
            if ($headEndPosition !== false) {
                $content = substr_replace($content, $codeHead . '</head>', $headEndPosition, 7);
            }
        }

        $response->setContent($content);
    }

    private function generateCode(): string
    {
        $html = '';
        foreach ($this->codeTracker->getBlocks() as $code) {
            $html .= $code . "\n";
        }

        return '<script>' . "\n" . $html . '</script>';
    }
}
