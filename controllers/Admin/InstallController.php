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

use CoreShop\Plugin;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_InstallController
 */
class CoreShop_Admin_InstallController extends Admin
{
    public function installAction()
    {
        $success = false;

        try {
            $install = new Plugin\Install();

            if($install->fullInstall()) {
                $success = true;
            }
        } catch (Exception $e) {
            \Pimcore\Logger::crit($e);

            throw $e;
        }

        $this->_helper->json(['success' => $success]);
    }
}
