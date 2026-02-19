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
use Pimcore\Tool\Console;
use Symfony\Component\Process\Process;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WkhtmltopdfProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly string $kernelRootDir,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createPdfFromString(string $content, array $params): string
    {
        $header = $params['headerTemplate'] ?? null;
        $footer = $params['footerTemplate'] ?? null;
        $orderDocument = $params['document'] ?? null;

        $event = new WkhtmlOptionsEvent($orderDocument);
        $this->eventDispatcher->dispatch(
            $event,
            sprintf('coreshop.order.%s.wkhtml.options', $orderDocument::getDocumentType()),
        );

        $bodyHtml = $this->createHtmlFile($content);
        $headerHtml = $this->createHtmlFile($header);
        $footerHtml = $this->createHtmlFile($footer);

        if (!is_array($params['options'] ?? null)) {
            $params['options'] = [];
        }

        $params['options']['*'] = $event->getOptions();

        if ($headerHtml) {
            $params['options']['--header-html'] = $headerHtml;
        }

        if ($footerHtml) {
            $params['options']['--footer-html'] = $footerHtml;
        }

        try {
            $pdfContent = $this->convert($bodyHtml, $params);
        } catch (\Exception $e) {
            throw new \Exception('error while converting pdf. message was: ' . $e->getMessage(), 0, $e);
        }

        return $pdfContent;
    }

    private function createHtmlFile(?string $string): ?string
    {
        if ($string) {
            $tmpHtmlFile = File::getLocalTempFilePath('html');

            file_put_contents($tmpHtmlFile, $this->replaceUrls($string));

            return $tmpHtmlFile;
        }

        return null;
    }

    /**
     * @param string $string
     */
    private function replaceUrls($string): string
    {
        $hostUrl = $this->kernelRootDir . '/public';
        $replacePrefix = '';

        //matches all links
        preg_match_all(
            "@(href|src)\s*=[\"']([^(http|mailto|javascript|data:|#)].*?(css|jpe?g|gif|png)?)[\"']@is",
            $string,
            $matches,
        );
        if (!empty($matches[0])) {
            foreach ($matches[0] as $key => $value) {
                $path = $matches[2][$key];

                if (str_starts_with($path, '//')) {
                    $absolutePath = 'http:' . $path;
                } elseif (str_starts_with($path, '/')) {
                    $absolutePath = preg_replace('@^' . $replacePrefix . '/@', '/', $path);
                    $absolutePath = $hostUrl . $absolutePath;
                } else {
                    $absolutePath = $hostUrl . "/$path";
                    if ($path[0] == '?') {
                        $absolutePath = $hostUrl . $path;
                    }
                    $netUrl = new \Net_URL2($absolutePath);
                    $absolutePath = $netUrl->getNormalizedURL();
                }

                $path = preg_quote($path);
                $string = preg_replace("!([\"'])$path([\"'])!is", '\\1' . $absolutePath . '\\2', $string);
            }
        }

        preg_match_all("@srcset\s*=[\"'](.*?)[\"']@is", $string, $matches);
        foreach ($matches[1] as $i => $value) {
            $parts = explode(',', $value);
            foreach ($parts as $key => $v) {
                $parts[$key] = $hostUrl . trim($v);
            }
            $s = ' srcset="' . implode(', ', $parts) . '" ';
            if ($matches[0][$i]) {
                $string = str_replace($matches[0][$i], $s, $string);
            }
        }

        return $string;
    }

    /**
     * Converts URL to pdf.
     *
     * @param string $httpSource
     * @param array $config
     *
     * @return string PDF-Content
     *
     * @throws \Exception
     */
    private function convert($httpSource, $config = []): string
    {
        $tmpPdfFile = File::getLocalTempFilePath('pdf');
        $options = ' ';
        $optionConfig = [];

        if (is_array($config['options'])) {
            foreach ($config['options'] as $argument => $value) {
                // there is no value only the option
                if (is_numeric($argument) || $argument === '*') {
                    $optionConfig[] = $value;
                } else {
                    $optionConfig[] = $argument . ' ' . $value;
                }
            }

            $options .= implode(' ', $optionConfig);
        }

        if (isset($config['bin'])) {
            $wkHtmlTopPfBinary = $config['bin'];
        } else {
            $wkHtmlTopPfBinary = $this->getWkHtmlToPdfBinary();
        }

        if (!$wkHtmlTopPfBinary) {
            throw new \Exception('wkhtmltopdf binary not found. please check your server configuration');
        }

        // use xvfb if possible
        if ($xvfb = $this->getXvfbBinary()) {
            $command = $xvfb . ' --auto-servernum --server-args="-screen 0, 1280x1024x24" ' . $wkHtmlTopPfBinary . ' --use-xserver ' . $options;
        } else {
            $command = $wkHtmlTopPfBinary . $options;
        }

        $process = Process::fromShellCommandline($command . ' ' . $httpSource . ' ' . $tmpPdfFile);
        $process->run();

        if (!file_exists($tmpPdfFile)) {
            throw new \Exception(
                sprintf(
                    'wkhtmltopdf pdf conversion failed. This could be a command error. Executed command was: "%s"',
                    $process->getCommandLine(),
                ),
            );
        }

        return file_get_contents($tmpPdfFile);
    }

    private function getWkHtmlToPdfBinary(): string
    {
        return (string) Console::getExecutable('wkhtmltopdf', true);
    }

    private function getXvfbBinary(): string
    {
        return (string) Console::getExecutable('xvfb-run', false);
    }
}
