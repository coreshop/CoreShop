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

use CoreShop\Plugin;
use CoreShop\Config;
use CoreShop\Tool;
use CoreShop\Helper\Country;

use CoreShop\Model;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_ThemeController extends Admin
{
    public function enableAction() {
        try {
            \CoreShop\Theme::enableTheme($this->getParam("theme"));

            $this->_helper->json(array("success" => true));
        } catch(\Exception $ex) {
            $this->_helper->json(array("success" => false, "message" => $ex->getMessage()));
        }
    }
}