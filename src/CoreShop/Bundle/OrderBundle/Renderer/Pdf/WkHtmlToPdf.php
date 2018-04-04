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

namespace CoreShop\Bundle\OrderBundle\Renderer\Pdf;

use Pimcore\Tool\Console;

final class WkHtmlToPdf implements PdfRendererInterface
{
    /**
     * @var string
     */
    private $kernelCacheDir;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @param string $kernelCacheDir
     * @param string $kernelRootDir
     */
    public function __construct($kernelCacheDir, $kernelRootDir)
    {
        $this->kernelCacheDir = $kernelCacheDir;
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function fromString($string, $header = '', $footer = '', $config = [])
    {
        $bodyHtml = $this->createHtmlFile($string);
        $headerHtml = $this->createHtmlFile($header);
        $footerHtml = $this->createHtmlFile($footer);

        if (!is_array($config['options'])) {
            $config['options'] = [];
        }

        $config['options']['--header-html'] = $headerHtml;
        $config['options']['--footer-html'] = $footerHtml;

        $pdfContent = $this->convert($bodyHtml, $config);


        $this->unlinkFile($bodyHtml);
        $this->unlinkFile($headerHtml);
        $this->unlinkFile($footerHtml);

        return $pdfContent;
    }

    /**
     * Creates an Temporary HTML File.
     *
     * @param $string
     *
     * @return string
     */
    private function createHtmlFile($string)
    {
        $tmpHtmlFile = $this->kernelCacheDir.'/'.uniqid().'.htm';

        file_put_contents($tmpHtmlFile, $this->replaceUrls($string));

        return $tmpHtmlFile;
    }

    private function replaceUrls($string)
    {
        $hostUrl = $this->kernelRootDir.'/web';
        $replacePrefix = '';

        //matches all links
        preg_match_all("@(href|src)\s*=[\"']([^(http|mailto|javascript|data:|#)].*?(css|jpe?g|gif|png)?)[\"']@is", $string, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $key => $value) {
                $path = $matches[2][$key];

                if (strpos($path, '//') === 0) {
                    $absolutePath = 'http:'.$path;
                } elseif (strpos($path, '/') === 0) {
                    $absolutePath = preg_replace('@^'.$replacePrefix.'/@', '/', $path);
                    $absolutePath = $hostUrl.$absolutePath;
                } else {
                    $absolutePath = $hostUrl."/$path";
                    if ($path[0] == '?') {
                        $absolutePath = $hostUrl.$path;
                    }
                    $netUrl = new \Net_URL2($absolutePath);
                    $absolutePath = $netUrl->getNormalizedURL();
                }

                $path = preg_quote($path);
                $string = preg_replace("!([\"'])$path([\"'])!is", '\\1'.$absolutePath.'\\2', $string);
            }
        }

        preg_match_all("@srcset\s*=[\"'](.*?)[\"']@is", $string, $matches);
        foreach ((array) $matches[1] as $i => $value) {
            $parts = explode(',', $value);
            foreach ($parts as $key => $v) {
                $parts[$key] = $hostUrl.trim($v);
            }
            $s = ' srcset="'.implode(', ', $parts).'" ';
            if ($matches[0][$i]) {
                $string = str_replace($matches[0][$i], $s, $string);
            }
        }

        return $string;
    }

    /**
     * Converts URL to pdf.
     *
     * @param $httpSource
     * @param array $config
     *
     * @return string PDF-Content
     *
     * @throws \Exception
     */
    private function convert($httpSource, $config = [])
    {
        $tmpPdfFile = $this->kernelCacheDir.'/'.uniqid().'.pdf';
        $options = ' ';
        $optionConfig = [];

        if (is_array($config['options'])) {
            foreach ($config['options'] as $argument => $value) {
                // there is no value only the option
                if (is_numeric($argument)) {
                    $optionConfig[] = $value;
                } else {
                    $optionConfig[] = $argument.' '.$value;
                }
            }

            $options .= implode(' ', $optionConfig);
        }

        $wkhtmltopdfBinary = $this->getWkhtmltodfBinary();

        if (isset($config['bin'])) {
            $wkhtmltopdfBinary = $config['bin'];
        }

        if ($wkhtmltopdfBinary) {
            Console::exec($wkhtmltopdfBinary.$options.' '.$httpSource.' '.$tmpPdfFile);

            $pdfContent = file_get_contents($tmpPdfFile);

            // remove temps
            $this->unlinkFile($tmpPdfFile);

            return $pdfContent;
        }

        throw new \Exception('wkhtmltopdf not found');
    }

    /**
     * @param string $file
     */
    private function unlinkFile($file)
    {
        @unlink($file);
    }

    /**
     * Find the wkhtmltopdf library.
     *
     * @return bool|string
     */
    private function getWkhtmltodfBinary()
    {
        return Console::getExecutable('wkhtmltopdf');
    }
}
