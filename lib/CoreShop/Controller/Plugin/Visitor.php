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

namespace CoreShop\Controller\Plugin;

use CoreShop\Model\Shop;
use CoreShop\Model\User;
use CoreShop\Model\Visitor\Page;
use CoreShop\Model\Visitor\Source;
use Pimcore\Tool;

/**
 * Class Visitor
 * @package CoreShop\Controller\Plugin
 */
class Visitor extends \Zend_Controller_Plugin_Abstract
{
    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  \Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        if (Tool::isFrontentRequestByAdmin()) {
            return;
        }

        if (Tool::isFrontend()) {
            $session = \CoreShop::getTools()->getSession();
            $visitor = \CoreShop::getTools()->getVisitor();

            //new Visitor
            if (!$visitor instanceof \CoreShop\Model\Visitor) {
                $visitor = \CoreShop\Model\Visitor::create();
                $visitor->setShopId(Shop::getShop()->getId());
                $visitor->setIp(ip2long(Tool::getClientIp()));
                $visitor->setController($request->getControllerName());
                $visitor->setAction($request->getActionName());
                $visitor->setModule($request->getModuleName());
                $visitor->setReferrer(\CoreShop::getTools()->getReferrer());
                $visitor->setCreationDate(time());

                if (\CoreShop::getTools()->getUser() instanceof User) {
                    $visitor->setUserId(\CoreShop::getTools()->getUser()->getId());
                }

                $visitor->save();

                $session->visitorId = $visitor->getId();
            } else {
                //recurring Visitor, set new page
                $page = Page::create();
                $page->setVisitor($visitor);
                $page->setController($request->getControllerName());
                $page->setAction($request->getActionName());
                $page->setModule($request->getModuleName());
                $page->setCreationDate(time());
                $page->save();

                if (\CoreShop::getTools()->getReferrer()) {
                    //recurring Visitor that comes from external: add new source
                    $source = Source::create();
                    $source->setVisitor($visitor);
                    $source->setPage($page);
                    $source->setReferrer(\CoreShop::getTools()->getReferrer());
                    $source->setRequestUrl($request->getRequestUri());
                    $source->setCreationDate(time());
                    $source->save();
                }
            }
        }
    }
}
