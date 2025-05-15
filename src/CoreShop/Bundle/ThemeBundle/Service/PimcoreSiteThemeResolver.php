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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document;
use Pimcore\Tool\Frontend;
use Symfony\Component\HttpFoundation\RequestStack;

final class PimcoreSiteThemeResolver implements ThemeResolverInterface, DocumentThemeResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private DocumentResolver $documentResolver,
    ) {
    }

    public function resolveTheme(): string
    {
        $request = $this->requestStack->getMainRequest();

        if (!$request) {
            throw new ThemeNotResolvedException();
        }

        $isAjaxBrickRendering = $request->attributes->get('_route') === 'pimcore_admin_document_page_areabrick-render-index-editmode';
        $document = null;

        if ($isAjaxBrickRendering) {
            $documentId = $request->request->get('documentId');

            if ($documentId) {
                $document = Document::getById((int) $documentId);
            }
        } else {
            $document = $this->documentResolver->getDocument($request);
        }

        if ($document instanceof Document) {
            return $this->resolveThemeForDocument($document);
        }

        throw new ThemeNotResolvedException();
    }

    public function resolveThemeForDocument(Document $document): string
    {
        $site = Frontend::getSiteForDocument($document);

        if ($site && $theme = $site->getRootDocument()->getKey()) {
            return $theme;
        }

        throw new ThemeNotResolvedException();
    }
}
