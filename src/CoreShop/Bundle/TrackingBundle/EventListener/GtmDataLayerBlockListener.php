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

declare(strict_types=1);

namespace CoreShop\Bundle\TrackingBundle\EventListener;

use Pimcore\Tool;
use Pimcore\Http\ResponseHelper;
use Pimcore\Analytics\SiteId\SiteIdProvider;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use CoreShop\Bundle\TrackingBundle\Tracker\Google\TagManager\CodeTracker;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class GtmDataLayerBlockListener
{
    protected PimcoreContextResolver $pimcoreContextResolver;
    protected ResponseHelper $responseHelper;
    protected SiteIdProvider $siteIdProvider;
    protected CodeTracker $codeTracker;

    public function __construct(
        PimcoreContextResolver $pimcoreContextResolver,
        ResponseHelper $responseHelper,
        SiteIdProvider $siteIdProvider,
        CodeTracker $codeTracker
    ) {
        $this->pimcoreContextResolver = $pimcoreContextResolver;
        $this->responseHelper = $responseHelper;
        $this->siteIdProvider = $siteIdProvider;
        $this->codeTracker = $codeTracker;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->pimcoreContextResolver->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        if (!Tool::useFrontendOutputFilters()) {
            return;
        }

        $serverVars = $event->getRequest()->server;
        if ($serverVars->get('HTTP_X_PURPOSE') === 'preview') {
            return;
        }

        $response = $event->getResponse();
        if (!$this->responseHelper->isHtmlResponse($response)) {
            return;
        }

        $codeHead = $this->generateCode();
        $content = $response->getContent();

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
