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

namespace CoreShop\Bundle\CoreBundle\Order\Renderer;

use CoreShop\Bundle\CoreBundle\Renderer\Pdf\PdfRendererInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

class OrderDocumentPdfRenderer implements OrderDocumentRendererInterface
{
    /**
     * @var FragmentRendererInterface
     */
    private $fragmentRenderer;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationHelper;

    /**
     * @var PdfRendererInterface
     */
    private $renderer;

    /**
     * @param FragmentRendererInterface     $fragmentRenderer
     * @param ConfigurationServiceInterface $configurationHelper
     * @param PdfRendererInterface          $renderer
     */
    public function __construct(FragmentRendererInterface $fragmentRenderer, ConfigurationServiceInterface $configurationHelper, PdfRendererInterface $renderer)
    {
        $this->fragmentRenderer = $fragmentRenderer;
        $this->configurationHelper = $configurationHelper;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDocumentPdf(OrderDocumentInterface $orderDocument)
    {
        $params = [
            'id' => $orderDocument->getId(),
            'order' => $orderDocument->getOrder(),
            'document' => $orderDocument,
            'language' => (string) $orderDocument->getOrder()->getOrderLanguage(),
            'type' => $orderDocument::getDocumentType(),
            $orderDocument::getDocumentType() => $orderDocument,
        ];

        $request = new Request($params);
        $request->setLocale($orderDocument->getOrder()->getOrderLanguage());

        $printBundle = 'CoreShopOrderBundle';
        $printController = 'OrderDocumentPrint';

        $printContentAction = $orderDocument::getDocumentType();
        $printFooterAction = 'footer';
        $printHeaderAction = 'header';

        $referenceFooter = new ControllerReference(sprintf('%s:%s:%s', $printBundle, $printController, $printFooterAction), $params);
        $referenceHeader = new ControllerReference(sprintf('%s:%s:%s', $printBundle, $printController, $printHeaderAction), $params);
        $referenceContent = new ControllerReference(sprintf('%s:%s:%s', $printBundle, $printController, $printContentAction), $params);

        $contentHeader = $this->fragmentRenderer->render($referenceHeader, $request)->getContent();
        $contentFooter = $this->fragmentRenderer->render($referenceFooter, $request)->getContent();
        $content = $this->fragmentRenderer->render($referenceContent, $request)->getContent();

        $options = $this->configurationHelper->getForStore(sprintf('system.%s.wkhtml', $orderDocument::getDocumentType()), $orderDocument->getOrder()->getStore());

        $pdfContent = $this->renderer->fromString($content, $contentHeader, $contentFooter, ['options' => [$options]]);

        return $pdfContent;
    }
}
