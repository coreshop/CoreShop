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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Controller\Plugin;

use CoreShop\Model\Configuration;
use CoreShop\Model\Shop;
use CoreShop\Plugin;
use Pimcore\Model\User;
use Pimcore\Tool;
use Pimcore\Tool\Authentication;

/**
 * Class Debug
 * @package CoreShop\Controller\Plugin
 */
class Debug extends \Zend_Controller_Plugin_Abstract
{
    /**
     * shutdown.
     */
    public function dispatchLoopShutdown()
    {
        if (!Tool::isHtmlResponse($this->getResponse())) {
            return;
        }

        if (!Tool::useFrontendOutputFilters($this->getRequest()) && !$this->getRequest()->getParam('pimcore_preview')) {
            return;
        }

        if (!Configuration::get('SYSTEM.BASE.SHOWDEBUG', Shop::getShop()->getId())) {
            return;
        }

        if (\Pimcore::inDebugMode() || isset($_COOKIE['pimcore_admin_sid'])) {
            $body = $this->getResponse()->getBody();

            $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $view = $viewRenderer->view;

            $view->setScriptPath(
                array_merge(
                    $view->getScriptPaths(),
                    array(
                        CORESHOP_PATH.'/views/scripts/debug',
                        CORESHOP_TEMPLATE_BASE.'/scripts/coreshop/debug',
                        CORESHOP_TEMPLATE_PATH.'/scripts/coreshop/debug',
                        PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop/debug',
                    )
                )
            );
            $view->getHelper('Translate')->setTranslator(Plugin::getTranslate(\Zend_Registry::get("Zend_Locale")));

            $code = $view->render('debug.php');

            // search for the end <head> tag, and insert the google analytics code before
            // this method is much faster than using simple_html_dom and uses less memory
            $bodyEndPosition = stripos($body, '</body>');
            if ($bodyEndPosition !== false) {
                $body = substr_replace($body, $code."\n\n</body>\n", $bodyEndPosition, 7);
            }

            $this->getResponse()->setBody($body);
        }
    }
}
