<?php
/**
 * CoreShop
 * LICENSE
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use Pimcore\Tool\Admin;

class CoreShop_Admin_UpdateController extends \Pimcore\Controller\Action\Admin {

    /**
     * @var $updater \CoreShop\Plugin\Update
     */
    var $updater = NULL;

    public function init()
    {
        parent::init();

        $this->updater = new \CoreShop\Plugin\Update();

        // clear the opcache (as of PHP 5.5)
        if (function_exists("opcache_reset"))
        {
            opcache_reset();
        }
        // clear the APC opcode cache (<= PHP 5.4)
        if (function_exists("apc_clear_cache"))
        {
            apc_clear_cache();
        }
        // clear the Zend Optimizer cache (Zend Server <= PHP 5.4)
        if (function_exists('accelerator_reset'))
        {
            accelerator_reset();
        }
    }

    public function getAvailableUpdatesAction()
    {

        $releases = $this->updater->getGitTagReleases();
        $latestMaster = $this->updater->getGitMasterCommit();

        /*

        $releases = array(

            0 => array(
                'sha' => 'v0.1',
                'date' => '',
                'message' => 'af5cf20c8cb016fe9ca7ead68e4a1fa6d1ffb13d',

            ),

            1 => array(
                'sha' => 'v0.2',
                'date' => '',
                'message' => 'af5cf20c8cb016fe9ca7ead68e4a1fa6d1ffb13d',

            )
        );


        $latestMaster = array(
            0 => array(
                'sha' => 'af5cf20c8cb016fe9ca7ead68e4a1fa6d1ffb13d',
                'date' => '',
                'message' => 'Build Version 27'
            )
        );

        */

        $this->_helper->json(array(
            "master" => $latestMaster,
            "releases" => $releases
        ));
    }

    public function checkFilePermissionsAction()
    {
        $success = FALSE;
        if ($this->updater->isWriteable())
        {
            $success = TRUE;
        }
        $this->_helper->json(array(
            "success" => $success
        ));
    }

    public function installRemoteUpdateAction()
    {
        $toRevision = $this->getParam('toRevision');
        $type = $this->getParam('type');

        $maintenanceModeId = 'cache-warming-dummy-session-id';
        Admin::activateMaintenanceMode($maintenanceModeId);

        $installer = $this->updater->installRelease( $type, $toRevision );

        Admin::deactivateMaintenanceMode();

        $this->_helper->json(array(
            'success' => $installer['success'],
            'message' => $installer['message']
        ));

    }

    /**
     * Live Update
     */
    public function hasUpdatesAction()
    {
        $hasUpdates = $this->updater->getAvailableBuildList() !== FALSE;

        if ($hasUpdates === FALSE)
        {
            $this->updater->removeUpdateFolder();
        }

        $this->_helper->json(array('hasUpdate' => $hasUpdates));
    }

    /**
     * Live Update
     * @throws \Exception
     */
    public function installUpdateAction()
    {
        $maintenanceModeId = 'cache-warming-dummy-session-id';
        Admin::activateMaintenanceMode($maintenanceModeId);

        $execution = $this->updater->updateCoreData();

        Admin::deactivateMaintenanceMode();

        $this->_helper->json(array('success' => TRUE, 'log' => $execution));
    }
}
