<?php

namespace CoreShop\Bundle\CoreBundle\Order\Renderer;

use CoreShop\Bundle\CoreBundle\Renderer\Pdf\PdfRendererInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

class AssetOrderDocumentPdfRenderer implements OrderDocumentRendererInterface
{
    /**
     * @var OrderDocumentRendererInterface
     */
    private $decoratedService;

    /**
     * AssetOrderDocumentPdfRenderer constructor.
     * @param OrderDocumentRendererInterface $decoratedService
     */
    public function __construct(OrderDocumentRendererInterface $decoratedService)
    {
        $this->decoratedService = $decoratedService;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDocumentPdf(OrderDocumentInterface $orderDocument)
    {
        if ($orderDocument->getRenderedAsset() instanceof Asset) {
            return $orderDocument->getRenderedAsset()->getData();
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