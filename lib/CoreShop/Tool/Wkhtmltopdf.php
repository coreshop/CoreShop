<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tool;

use CoreShop\Exception;
use Pimcore\Tool;
use Pimcore\Tool\Console;

/**
 * Class Wkhtmltopdf
 * @package CoreShop\Tool
 */
class Wkhtmltopdf
{
    /**
     * Converts HTML from url to PDF.
     *
     * @param $url
     * @param array $config
     *
     * @return string PDF-Content
     *
     * @throws Exception
     */
    public static function fromUrl($url, $config = [])
    {
        return self::convert($url, $config);
    }

    /**
     * Converts HTML from String to pdf.
     *
     * @param $string
     * @param $header
     * @param $footer
     * @param array $config
     *
     * @return string PDF-Content
     *
     * @throws Exception
     */
    public static function fromString($string, $header = '', $footer = '', $config = [])
    {
        $bodyHtml = self::createHtmlFile($string);
        $headerHtml = self::createHtmlFile($header);
        $footerHtml = self::createHtmlFile($footer);

        if (!is_array($config['options'])) {
            $config['options'] = [];
        }

        $config['options']['--header-html'] = $headerHtml['absolutePath'];
        $config['options']['--footer-html'] = $footerHtml['absolutePath'];

        $pdfContent = self::convert($bodyHtml['absolutePath'], $config);

        @unlink($bodyHtml['relativePath']);
        @unlink($headerHtml['relativePath']);
        @unlink($footerHtml['relativePath']);

        return $pdfContent;
    }

    /**
     * Creates an Temporary HTML File.
     *
     * @param $string
     *
     * @return array( absolutePath, relativePath )
     */
    protected static function createHtmlFile($string)
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
     * @throws Exception
     */
    protected static function convert($httpSource, $config = [])
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

        $wkhtmltopdfBinary = self::getWkhtmltodfBinary();

        if ($config['bin']) {
            $wkhtmltopdfBinary = $config['bin'];
        }

        if ($wkhtmltopdfBinary) {
            Console::exec($wkhtmltopdfBinary.$options.' '.$httpSource.' '.$tmpPdfFile);

            $pdfContent = file_get_contents($tmpPdfFile);

            // remove temps
            @unlink($tmpPdfFile);

            return $pdfContent;
        }

        throw new Exception('wkhtmltopdf not found');
    }

    /**
     * Find the wkhtmltopdf library.
     *
     * @return bool|string
     */
    protected static function getWkhtmltodfBinary()
    {
        return Console::getExecutable("wkhtmltopdf");
    }
}
