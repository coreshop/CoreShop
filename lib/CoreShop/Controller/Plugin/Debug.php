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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Controller\Plugin;

use CoreShop\Model\Configuration;
use CoreShop\Plugin;
use Pimcore\Model\User;
use Pimcore\Tool;
use Pimcore\Tool\Authentication;

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

        if (!Configuration::get('SYSTEM.BASE.SHOWDEBUG')) {
            return;
        }

        if (isset($_COOKIE['pimcore_admin_sid'])) {
            $user = Authentication::authenticateSession();

            if ($user instanceof User) {
                $body = $this->getResponse()->getBody();

                $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
                $view = $viewRenderer->view;

                $view->setScriptPath(
                    array_merge(
                        $view->getScriptPaths(),
                        array(
                            CORESHOP_PATH.'/views/scripts/debug',
                        )
                    )
                );
                $view->getHelper('Translate')->setTranslator(Plugin::getTranslate($user->getLanguage()));

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
}
