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

class CoreShop_Admin_UpdateController extends \Pimcore\Controller\Action\Admin
{
    public function init() {
        parent::init();

        // clear the opcache (as of PHP 5.5)
        if(function_exists("opcache_reset")) {
            opcache_reset();
        }

        // clear the APC opcode cache (<= PHP 5.4)
        if(function_exists("apc_clear_cache")) {
            apc_clear_cache();
        }

        // clear the Zend Optimizer cache (Zend Server <= PHP 5.4)
        if (function_exists('accelerator_reset')) {
            accelerator_reset();
        }
    }

    public function checkFilePermissionsAction () {

        $success = false;
        if(\CoreShop\Update::isWriteable()) {
            $success = true;
        }

        $this->_helper->json(array(
            "success" => $success
        ));
    }

    public function getAvailableUpdatesAction () {

        $availableUpdates = \CoreShop\Update::getAvailableUpdates();
        $this->_helper->json($availableUpdates);
    }

    public function getJobsAction () {

        $jobs = \CoreShop\Update::getJobs($this->getParam("toRevision"));

        $this->_helper->json($jobs);
    }

    public function jobParallelAction () {
        if($this->getParam("type") == "download") {
            \CoreShop\Update::downloadData($this->getParam("revision"), $this->getParam("url"), $this->getParam("file"));
        }

        $this->_helper->json(array("success" => true));
    }

    public function jobProceduralAction () {

        $status = array("success" => true);

        if($this->getParam("type") == "files") {
            \CoreShop\Update::installData($this->getParam("revision"));
        } else if ($this->getParam("type") == "clearcache") {
            \Pimcore\Cache::clearAll();
        } else if ($this->getParam("type") == "preupdate") {
            $status = \CoreShop\Update::executeScript($this->getParam("revision"), "preupdate");
        } else if ($this->getParam("type") == "postupdate") {
            $status = \CoreShop\Update::executeScript($this->getParam("revision"), "postupdate");
        } else if ($this->getParam("type") == "cleanup") {
            \CoreShop\Update::cleanup();
        }

        // we use pure PHP here, otherwise this can cause issues with dependencies that changed during the update
        header("Content-type: application/json");
        echo json_encode($status);
        exit;
    }
}