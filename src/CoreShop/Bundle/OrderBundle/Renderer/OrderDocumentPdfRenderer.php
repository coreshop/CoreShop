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

namespace CoreShop\Bundle\OrderBundle\Renderer;

use CoreShop\Bundle\OrderBundle\Event\WkhtmlOptionsEvent;
use CoreShop\Bundle\OrderBundle\Renderer\Pdf\PdfRendererInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var PdfRendererInterface
     */
    private $renderer;

    /**
     * @var ThemeHelperInterface
     */
    private $themeHelper;

    /**
     * @param FragmentRendererInterface $fragmentRenderer
     * @param EventDispatcherInterface  $eventDispatcher
     * @param PdfRendererInterface      $renderer
     * @param ThemeHelperInterface      $themeHelper
     */
    public function __construct(
        FragmentRendererInterface $fragmentRenderer,
        EventDispatcherInterface $eventDispatcher,
        PdfRendererInterface $renderer,
        ThemeHelperInterface $themeHelper
    ) {
        $this->fragmentRenderer = $fragmentRenderer;
        $this->eventDispatcher = $eventDispatcher;
        $this->renderer = $renderer;
        $this->themeHelper = $themeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function renderDocumentPdf(OrderDocumentInterface $orderDocument)
    {
        return $this->themeHelper->useTheme($orderDocument->getOrder()->getStore()->getTemplate(), function () use ($orderDocument) {
            $params = [
                'id' => $orderDocument->getId(),
                'order' => $orderDocument->getOrder(),
                'document' => $orderDocument,
                'language' => (string) $orderDocument->getOrder()->getLocaleCode(),
                'type' => $orderDocument::getDocumentType(),
                $orderDocument::getDocumentType() => $orderDocument,
            ];

            $request = new Request($params);
            $request->setLocale($orderDocument->getOrder()->getLocaleCode());

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

            $event = new WkhtmlOptionsEvent($orderDocument);

            $this->eventDispatcher->dispatch(sprintf('coreshop.order.%s.wkhtml.options', $orderDocument::getDocumentType()), $event);

            return $this->renderer->fromString($content, $contentHeader, $contentFooter, ['options' => [$event->getOptions()]]);
        });
    }
}
