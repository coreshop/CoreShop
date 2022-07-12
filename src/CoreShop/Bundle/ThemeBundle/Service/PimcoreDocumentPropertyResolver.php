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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Http\Request\Resolver\SiteResolver;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\RequestStack;

final class PimcoreDocumentPropertyResolver implements ThemeResolverInterface, DocumentThemeResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private DocumentResolver $documentResolver,
        private Document\Service $documentService,
        private SiteResolver $siteResolver
    ) {
    }

    public function resolveTheme(): string
    {
        try {
            $request = $this->requestStack->getMainRequest();

            if (!$request) {
                throw new ThemeNotResolvedException();
            }

            $site = $this->siteResolver->getSite($request);

            $isAjaxBrickRendering = $request->attributes->get('_route') === 'pimcore_admin_document_page_areabrick-render-index-editmode';
            $document = null;

            if ($isAjaxBrickRendering) {
                $documentId = $request->request->get('documentId');

                if ($documentId) {
                    $document = Document::getById((int)$documentId);
                }
            }
            else {
                $document = $this->documentResolver->getDocument($request);
            }

            if (!$document) {
                $basePath = '';

                if ($site instanceof Site) {
                    $basePath = $site->getRootPath();
                }

                /**
                 * @psalm-suppress InternalMethod
                 */
                $document = $this->documentService->getNearestDocumentByPath($basePath . $request->getPathInfo());
            }

            if ($document instanceof Document && $document->getProperty('theme')) {
                return $document->getProperty('theme');
            }
        } catch (\Exception $ex) {
            throw new ThemeNotResolvedException($ex);
        }

        throw new ThemeNotResolvedException();
    }

    public function resolveThemeForDocument(Document $document): string
    {
        if ($document->getProperty('theme')) {
            return $document->getProperty('theme');
        }

        throw new ThemeNotResolvedException();
    }
}
