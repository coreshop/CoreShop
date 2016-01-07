<?php

/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Tool;

use CoreShop\Exception;
use Pimcore\Config;
use Pimcore\Tool\Console;

class Wkhtmltopdf {

    /**
     * Converts HTML from url to PDF
     *
     * @param $url
     * @param array $config
     * @return string PDF-Content
     *
     * @throws Exception
     */
    public static function fromUrl ($url, $config = array()) {
        return self::convert($url, $config);
    }

    /**
     * Converts HTML from String to pdf
     *
     * @param $string
     * @param array $config
     * @return string PDF-Content
     *
     * @throws Exception
     */
    public static function fromString ($string, $header = "", $footer = "", $config = array()) {

        $bodyHtml = self::createHtmlFile($string);
        $headerHtml = self::createHtmlFile($header);
        $footerHtml = self::createHtmlFile($footer);

        if(!is_array($config['options'])) {
            $config['options'] = array();
        }

        $config['options']['--header-html'] = $headerHtml;
        $config['options']['--footer-html'] = $footerHtml;

        $pdfContent = self::convert($bodyHtml, $config);

        @unlink($bodyHtml);
        @unlink($headerHtml);
        @unlink($footerHtml);

        return $pdfContent;
    }

    /**
     * Creates an Temporary HTML File
     *
     * @param $string
     * @return string
     */
    protected static function createHtmlFile($string) {
        $tmpHtmlFile = PIMCORE_TEMPORARY_DIRECTORY . "/" . uniqid() . ".htm";
        file_put_contents($tmpHtmlFile, $string);
        $httpSource = $_SERVER["HTTP_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . str_replace($_SERVER["DOCUMENT_ROOT"],"",$tmpHtmlFile);

        return $httpSource;
    }

    /**
     * Converts URL to pdf
     *
     * @param $httpSource
     * @param array $config
     * @return string PDF-Content
     *
     * @throws Exception
     */
    protected static function convert ($httpSource, $config = array()) {

        $tmpPdfFile = PIMCORE_SYSTEM_TEMP_DIRECTORY . "/" . uniqid() . ".pdf";
        $options = " ";
        $optionConfig = array();

        if(is_array($config["options"])) {
            foreach ($config["options"] as $argument => $value) {
                // there is no value only the option
                if(is_numeric($argument)) {
                    $optionConfig[] = $value;
                } else {
                    $optionConfig[] = $argument . " " . $value;
                }
            }

            $options .= implode(" ", $optionConfig);
        }

        $wkhtmltopdfBinary = self::getWkhtmltodfBinary();

        if($config["bin"]) {
            $wkhtmltopdfBinary = $config["bin"];
        }

        if($wkhtmltopdfBinary) {
            Console::exec($wkhtmltopdfBinary . $options . " " . $httpSource . " " . $tmpPdfFile);

            $pdfContent = file_get_contents($tmpPdfFile);

            // remove temps
            @unlink($tmpPdfFile);

            return $pdfContent;
        }

        throw new Exception("wkhtmltopdf not found");
    }

    /**
     * Find the wkhtmltopdf library
     *
     * @return bool|string
     */
    protected static function getWkhtmltodfBinary () {

        if(Config::getSystemConfig()->documents->wkhtmltopdf) {
            if(@is_executable(Config::getSystemConfig()->documents->wkhtmltopdf)) {
                return (string) Config::getSystemConfig()->documents->wkhtmltopdf;
            } else {
                \Logger::critical("wkhtmltopdf binary: " . Config::getSystemConfig()->documents->wkhtmltopdf . " is not executable");
            }
        }

        $paths = array(
            "/usr/bin/wkhtmltopdf-amd64",
            "/usr/local/bin/wkhtmltopdf-amd64",
            "/bin/wkhtmltopdf-amd64",
            "/usr/bin/wkhtmltopdf",
            "/usr/local/bin/wkhtmltopdf",
            "/bin/wkhtmltopdf",
            realpath(PIMCORE_DOCUMENT_ROOT . "/../wkhtmltox/wkhtmltopdf.exe") // for windows sample package (XAMPP)
        );

        foreach ($paths as $path) {
            if(@is_executable($path)) {
                return $path;
            }
        }

        return false;
    }

}