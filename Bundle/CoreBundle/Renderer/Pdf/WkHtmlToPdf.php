<?php

namespace CoreShop\Bundle\CoreBundle\Renderer\Pdf;

use Pimcore\Tool;
use Pimcore\Tool\Console;

final class WkHtmlToPdf implements PdfRendererInterface
{
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

        $config['options']['--header-html'] = $headerHtml['absolutePath'];
        $config['options']['--footer-html'] = $footerHtml['absolutePath'];

        $pdfContent = $this->convert($bodyHtml['absolutePath'], $config);

        $this->unlinkFile($bodyHtml['relativePath']);
        $this->unlinkFile($headerHtml['relativePath']);
        $this->unlinkFile($footerHtml['relativePath']);

        return $pdfContent;
    }

    /**
     * Creates an Temporary HTML File.
     *
     * @param $string
     *
     * @return array including absolutePath and relativePath
     */
    protected function createHtmlFile($string)
    {
        $tmpHtmlFile = PIMCORE_TEMPORARY_DIRECTORY.'/'.uniqid().'.htm';
        file_put_contents($tmpHtmlFile, $string);
        $httpSource = rtrim(Tool::getHostUrl(), '/') . '/' .str_replace($_SERVER['DOCUMENT_ROOT'], '', $tmpHtmlFile);

        return ['absolutePath' => $httpSource, 'relativePath' => $tmpHtmlFile];
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
    protected function convert($httpSource, $config = [])
    {
        $tmpPdfFile = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/'.uniqid().'.pdf';
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
    protected function unlinkFile($file)
    {
        @unlink($file);
    }

    /**
     * Find the wkhtmltopdf library.
     *
     * @return bool|string
     */
    protected function getWkhtmltodfBinary()
    {
        return Console::getExecutable("wkhtmltopdf");
    }
}