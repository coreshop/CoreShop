<?php
/**
 * CoreShop
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