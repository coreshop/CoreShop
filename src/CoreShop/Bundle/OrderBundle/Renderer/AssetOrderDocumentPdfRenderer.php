<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Renderer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Pimcore\Model\Asset;

class AssetOrderDocumentPdfRenderer implements OrderDocumentRendererInterface
{
    /**
     * @var OrderDocumentRendererInterface
     */
    private $decoratedService;

    /**
     * @var string
     */
    private $environment;

    /**
     * @param OrderDocumentRendererInterface $decoratedService
     * @param string $environment
     */
    public function __construct(OrderDocumentRendererInterface $decoratedService, string $environment)
    {
        $this->decoratedService = $decoratedService;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDocumentPdf(OrderDocumentInterface $orderDocument)
    {
        // if in dev mode, do not store document
        if ($this->environment === 'dev') {
            return $this->decoratedService->renderDocumentPdf($orderDocument);
        }

        if ($orderDocument->getRenderedAsset() instanceof Asset) {
            // check if asset is outdated.
            if ((int)$orderDocument->getRenderedAsset()->getCreationDate() >= (int)$orderDocument->getModificationDate()) {
                return $orderDocument->getRenderedAsset()->getData();
            }
        }

        $pdfContent = $this->decoratedService->renderDocumentPdf($orderDocument);

        $assetPath = $orderDocument->getFullPath();
        $assetName = sprintf('%s.pdf', $orderDocument::getDocumentType());

        $document = Asset\Document::getByPath($assetPath.'/'.$assetName);

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
