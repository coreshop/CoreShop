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

if (!defined("CORESHOP_PATH"))  define("CORESHOP_PATH", PIMCORE_PLUGINS_PATH. "/CoreShop");
if (!defined("CORESHOP_CONFIGURATION_PATH"))  define("CORESHOP_CONFIGURATION_PATH", PIMCORE_CONFIGURATION_DIRECTORY);
if (!defined("CORESHOP_CONFIGURATION"))  define("CORESHOP_CONFIGURATION", CORESHOP_CONFIGURATION_PATH . "/coreshop-config.xml");

$config = \CoreShop\Config::getConfig();

$template = $config->template->name;

if(!$template) {
    die("No template configured");
}

$templateBasePath = '';

if( is_dir(PIMCORE_WEBSITE_PATH . '/views/coreshop/template/' . $template ) ) {

    $templateBasePath = PIMCORE_WEBSITE_PATH . "/views/coreshop/template";
    $templateResources =  "/website/views/coreshop/template/". $template . "/static/";

} else {

    $templateBasePath = CORESHOP_PATH . "/views/template";
    $templateResources = "/plugins/CoreShop/views/template/" . $template . "/static/";

}

if (!defined("CORESHOP_TEMPLATE_BASE_PATH"))  define("CORESHOP_TEMPLATE_BASE_PATH", $templateBasePath);
if (!defined("CORESHOP_TEMPLATE_NAME"))  define("CORESHOP_TEMPLATE_NAME", $template);
if (!defined("CORESHOP_TEMPLATE_PATH"))  define("CORESHOP_TEMPLATE_PATH", CORESHOP_TEMPLATE_BASE_PATH . "/" . $template);
if (!defined("CORESHOP_TEMPLATE_RESOURCES"))  define("CORESHOP_TEMPLATE_RESOURCES", $templateResources);

if(!is_dir(CORESHOP_TEMPLATE_PATH)) {
    die(sprintf("Template with name '%s' not found. (%s)", $template, CORESHOP_TEMPLATE_PATH));
}
