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
namespace CoreShop\Controller\Plugin;

use Pimcore\Tool;

class TemplateRouter extends \Zend_Controller_Plugin_Abstract {

    /**
     * Checks if Controller is available in Template and use it
     *
     * @param \Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(\Zend_Controller_Request_Abstract $request)
    {
        $coreShopRequest = clone $request;
        if($request->getModuleName() === "CoreShop") {
            //Check if TemplateController is available
            $frontController = \Zend_Controller_Front::getInstance();

            $coreShopRequest->setModuleName("CoreShopTemplate");

            if($frontController->getDispatcher()->isDispatchable($coreShopRequest)) {
                $request->setModuleName("CoreShopTemplate");
            }
        }
    }
}
