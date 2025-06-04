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

namespace CoreShop\Bundle\OrderBundle\Renderer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Pimcore\Model\Asset;

class AssetOrderDocumentPdfRenderer implements OrderDocumentRendererInterface
{
    public function __construct(
        private OrderDocumentRendererInterface $decoratedService,
        private string $environment,
    ) {
    }

    public function renderDocumentPdf(OrderDocumentInterface $orderDocument): string
    {
        // if in dev mode, do not store document
        if ($this->environment === 'dev') {
            return $this->decoratedService->renderDocumentPdf($orderDocument);
        }

        if ($orderDocument->getRenderedAsset() instanceof Asset) {
            // check if asset is outdated.
            if ($orderDocument->getRenderedAsset()->getCreationDate() >= $orderDocument->getModificationDate()) {
                return $orderDocument->getRenderedAsset()->getData();
            }
        }

        $pdfContent = $this->decoratedService->renderDocumentPdf($orderDocument);

        $assetPath = $orderDocument->getFullPath();
        $assetName = sprintf('%s.pdf', $orderDocument::getDocumentType());

        $document = Asset\Document::getByPath($assetPath . '/' . $assetName);

        if ($document instanceof Asset\Document) {
            $document->delete();
        }

        $document = new Asset\Document();
        $document->setFilename($assetName);
        $document->setParent(Asset\Service::createFolderByPath($assetPath));
        $document->setData($pdfContent);
        $document->save();

        $orderDocument->setRenderedAsset($document);
        $orderDocument->save();

        return $document->getData();
    }
}
