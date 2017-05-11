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

namespace CoreShop\Bundle\CoreBundle\Renderer\Pdf;

use Pimcore\Tool;
use Pimcore\Tool\Console;

final class WkHtmlToPdf implements PdfRendererInterface
{
    /**
     * @var string
     */
    private $kernelCacheDir;

    /**
     * @param string $kernelCacheDir
     */
    public function __construct($kernelCacheDir)
    {
        $this->kernelCacheDir = $kernelCacheDir;
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
        file_put_contents($tmpHtmlFile, $string);

        return $tmpHtmlFile;
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
