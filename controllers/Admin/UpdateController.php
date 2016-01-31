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

use Pimcore\Tool\Admin;

class CoreShop_Admin_UpdateController extends \Pimcore\Controller\Action\Admin
{
    public function init()
    {
        parent::init();
        // clear the opcache (as of PHP 5.5)
        if (function_exists("opcache_reset")) {
            opcache_reset();
        }
        // clear the APC opcode cache (<= PHP 5.4)
        if (function_exists("apc_clear_cache")) {
            apc_clear_cache();
        }
        // clear the Zend Optimizer cache (Zend Server <= PHP 5.4)
        if (function_exists('accelerator_reset')) {
            accelerator_reset();
        }
    }

    public function hasUpdatesAction()
    {
        $updater = new \CoreShop\Plugin\Update();

        $hasUpdates = $updater->getAvailableBuildList() !== false;

        if ($hasUpdates === false) {
            $updater->removeUpdateFolder();
        }

        $this->_helper->json(array('hasUpdate' => $hasUpdates));
    }

    public function installUpdateAction()
    {
        $maintenanceModeId = 'cache-warming-dummy-session-id';
        Admin::activateMaintenanceMode($maintenanceModeId);

        $updater = new \CoreShop\Plugin\Update();

        $execution = $updater->updateCoreData();

        Admin::deactivateMaintenanceMode();

        $this->_helper->json(array('success' => true, 'log' => $execution));
    }
}
