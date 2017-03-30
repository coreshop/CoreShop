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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Tracking\Google\TagManager\Controller;

use Pimcore\Tool;

/**
 * Class Plugin
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Tracking\Google\TagManager\Controller\Plugin
 */
class Plugin extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * @var array
     */
    protected static $dataLayer = [];

    /**
     * @param array $data
     */
    public static function addDataLayer($data)
    {
        self::$dataLayer = array_merge(self::$dataLayer, $data);
    }

    /**
     * @param \Zend_Controller_Request_Abstract $request
     * @return bool
     */
    public function routeShutdown(\Zend_Controller_Request_Abstract $request)
    {
        if (!Tool::useFrontendOutputFilters($request)) {
            return $this->disable();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function disable()
    {
        $this->enabled = false;

        return true;
    }

    /**
     *
     */
    public function dispatchLoopShutdown()
    {
        if (!Tool::isHtmlResponse($this->getResponse())) {
            return;
        }

        $siteKey = \Pimcore\Tool\Frontend::getSiteKey();
        $reportConfig = \Pimcore\Config::getReportConfig();

        if ($this->enabled && isset($reportConfig->tagmanager->sites->$siteKey->containerId)) {
            $containerId = $reportConfig->tagmanager->sites->$siteKey->containerId;

            $dataLayer = \Zend_Json::encode(self::$dataLayer);

            if ($containerId) {
                $code = <<<CODE
                <script type="text/javascript">
                    var dataLayer = [];
                    
                    dataLayer.push({
                        ecommerce : $dataLayer
                    });
                </script>
                <!-- Google Tag Manager -->
                <noscript><iframe src="//www.googletagmanager.com/ns.html?id=$containerId"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','$containerId');</script>
                <!-- End Google Tag Manager -->
CODE;

                $body = $this->getResponse()->getBody();

                // insert code after the opening <body> tag
                $body = preg_replace("@<body(>|.*?[^?]>)@", "<body$1\n\n" . $code, $body);

                $this->getResponse()->setBody($body);
            }
        }
    }
}
