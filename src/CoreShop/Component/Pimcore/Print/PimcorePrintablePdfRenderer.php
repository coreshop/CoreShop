<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Pimcore\Print;

use Pimcore\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

class PimcorePrintablePdfRenderer implements PrintablePdfRendererInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private FragmentRendererInterface $fragmentRenderer,
    ) {
    }

    public function renderPrintable(PrintableInterface $printable, array $params = []): string
    {
        $request = new Request($params);

        if (isset($params['locale']) && $params['locale']) {
            $request->setLocale($params['locale']);
        }

        $params['printable'] = $printable;

        $referenceContent = new ControllerReference($printable->getPrintBodyController($params), $params);
        $referenceHeader = new ControllerReference($printable->getPrintHeaderController($params), $params);
        $referenceFooter = new ControllerReference($printable->getPrintFooterController($params), $params);

        $contentHeader = $this->fragmentRenderer->render($referenceHeader, $request)->getContent();
        $contentFooter = $this->fragmentRenderer->render($referenceFooter, $request)->getContent();
        $content = $this->fragmentRenderer->render($referenceContent, $request)->getContent();

        /**
         * @psalm-suppress InternalMethod
         */
        $contentHeaderFile = File::getLocalTempFilePath('html');

        /**
         * @psalm-suppress InternalMethod
         */
        $contentFooterFile = File::getLocalTempFilePath('html');

        file_put_contents($contentHeaderFile, $contentHeader ?: '');
        file_put_contents($contentFooterFile, $contentFooter ?: '');

        $params = array_merge($params['printerOptions'] ?? [], [
            'headerTemplate' => $contentHeaderFile,
            'footerTemplate' => $contentFooterFile,
            'marginTop' => 1,
            'document' => $printable,
        ]);

        return $this->processor->createPdfFromString(
            $content ?: '',
            $params,
        );
    }
}
