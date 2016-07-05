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

use CoreShop\Plugin;
use Pimcore\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_ThemeController
 */
class CoreShop_Admin_ThemeController extends Admin
{
    public function enableAction()
    {
        try {
            Plugin::enableTheme($this->getParam('theme'));

            $this->_helper->json(array('success' => true));
        } catch (\CoreShop\Exception $ex) {
            $this->_helper->json(array('success' => false, 'message' => $ex->getMessage()));
        }
    }
}
