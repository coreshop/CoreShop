<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Renderer;

use CoreShop\Bundle\OrderBundle\Controller\OrderDocumentPrintController;
use CoreShop\Bundle\OrderBundle\Event\WkhtmlOptionsEvent;
use CoreShop\Bundle\OrderBundle\Renderer\Pdf\PdfRendererInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderDocumentPdfRenderer implements OrderDocumentRendererInterface
{
    public function __construct(private FragmentRendererInterface $fragmentRenderer, private EventDispatcherInterface $eventDispatcher, private PdfRendererInterface $renderer, private ThemeHelperInterface $themeHelper)
    {
    }

    public function renderDocumentPdf(OrderDocumentInterface $orderDocument): string
    {
        return $this->themeHelper->useTheme($orderDocument->getOrder()->getStore()->getTemplate(), function () use ($orderDocument) {
            $params = [
                'id' => $orderDocument->getId(),
                'order' => $orderDocument->getOrder(),
                'document' => $orderDocument,
                'language' => (string)$orderDocument->getOrder()->getLocaleCode(),
                'type' => $orderDocument::getDocumentType(),
                $orderDocument::getDocumentType() => $orderDocument,
            ];

            $request = new Request($params);
            $request->setLocale($orderDocument->getOrder()->getLocaleCode());

            $printController = OrderDocumentPrintController::class;

            $printContentAction = $orderDocument::getDocumentType() . 'Action';
            $printFooterAction = 'footerAction';
            $printHeaderAction = 'headerAction';

            $referenceFooter = new ControllerReference(sprintf('%s::%s', $printController, $printFooterAction), $params);
            $referenceHeader = new ControllerReference(sprintf('%s::%s', $printController, $printHeaderAction), $params);
            $referenceContent = new ControllerReference(sprintf('%s::%s', $printController, $printContentAction), $params);

            $contentHeader = $this->fragmentRenderer->render($referenceHeader, $request)->getContent();
            $contentFooter = $this->fragmentRenderer->render($referenceFooter, $request)->getContent();
            $content = $this->fragmentRenderer->render($referenceContent, $request)->getContent();

            $event = new WkhtmlOptionsEvent($orderDocument);

            $this->eventDispatcher->dispatch(
                $event,
                sprintf('coreshop.order.%s.wkhtml.options', $orderDocument::getDocumentType())
            );

            return $this->renderer->fromString(
                $content ?: '',
                $contentHeader ?: '',
                $contentFooter ?: '',
                ['options' => [$event->getOptions()]]
            );
        });
    }
}
