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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Renderer;

use CoreShop\Bundle\OrderBundle\Controller\OrderDocumentPrintController;
use CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Renderer\OrderDocumentRendererInterface;
use Pimcore\Bundle\WebToPrintBundle\Processor;
use Pimcore\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

class PimcoreOrderDocumentPdfRenderer implements OrderDocumentRendererInterface
{
    public function __construct(
        private readonly FragmentRendererInterface $fragmentRenderer,
        private readonly ThemeHelperInterface $themeHelper,
    ) {
    }

    public function renderDocumentPdf(OrderDocumentInterface $orderDocument): string
    {
        return $this->themeHelper->useTheme($orderDocument->getOrder()->getStore()->getTemplate(), function () use ($orderDocument) {
            /**
             * Gotenberg Supported for now, Chromium Headless not properly tested
             * */

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

            $contentHeaderFile = File::getLocalTempFilePath('html');
            $contentFooterFile = File::getLocalTempFilePath('html');

            file_put_contents($contentHeaderFile, $contentHeader);
            file_put_contents($contentFooterFile, $contentFooter);

            $params = [
                'headerTemplate' => $contentHeaderFile,
                'footerTemplate' => $contentFooterFile,
                'marginTop' => 1,
            ];

            return Processor::getInstance()->getPdfFromString(
                $content,
                $params
            );
        });
    }
}
