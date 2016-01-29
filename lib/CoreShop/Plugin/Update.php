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

namespace CoreShop\Plugin;

use Pimcore\Model\Tool\Setup;
use Pimcore\Cache;

use CoreShop\Version;
use CoreShop\Model\Configuration;

class Update
{

    /**
     * @var bool
     */
    private $dryRun = FALSE;

    public function setDryRun( $mode = TRUE ) {

        $this->dryRun = $mode;

        return $this;

    }

    /**
     * Get available builds to install.
     * Mostly used by console command.
     *
     * @return bool
     */
    public function getAvailableBuildList() {

        $buildState = $this->getBuildStatus();

        if( $buildState === FALSE )
            return FALSE;

        return $this->getBuilds( $buildState['installed'], $buildState['newest'] );

    }

    /**
     *
     * @Todo: Implement Version Updates.
     * @Todo: Check if in Backend && logged in user?
     *
     * @return bool
     */
    public function updateCoreData()
    {
        $buildState = $this->getBuildStatus();

        if( $buildState === FALSE )
            return FALSE;

        $availableBuilds = $this->getBuilds( $buildState['installed'], $buildState['newest'] );

        if( !empty( $availableBuilds ) )
        {
            $execution = $this->executeBuildUpdates( $availableBuilds );

            if( $execution['success'] == TRUE )
            {
                //clear cache and kill update folder.
                $this->cleanUp( $buildState['newest'] );

                return $execution;

            }

        }

        return FALSE;

    }


    private function getBuildStatus()
    {
        $currentBuild = (int) Version::getBuildNumber();
        $installedBuild = Configuration::get("SYSTEM.BASE.BUILD");

        if( $currentBuild <= $installedBuild )
            return FALSE;

        return array( 'newest' => (int) $currentBuild, 'installed' => (int) $installedBuild );

    }

    private function updateCoreShopBuild( $toBuild = 0)
    {
        return Configuration::set("SYSTEM.BASE.BUILD", $toBuild);
    }

    private function getBuilds( $fromBuild = 0, $toBuild = 0)
    {
        if( $toBuild < $fromBuild )
            return FALSE;

        $builds = array();

        $newBuild = $fromBuild;

        while( $newBuild < $toBuild ) {

            $newBuild++;

            $buildDir = CORESHOP_UPDATE_DIRECTORY . "/" . $newBuild;

            if( !is_dir( $buildDir ) )
                continue;

            $scriptFile = $buildDir . "/postupdate.php";
            $QueryFile = $buildDir . "/query.sql";

            if( !is_file( $scriptFile ) )
                continue;

            $builds[] = array(
                'build' => $newBuild,
                'script' => $scriptFile,
                'query' => is_file( $QueryFile ) ? $QueryFile : FALSE,
            );

        }

        return $builds;

    }

    private function executeBuildUpdates( $builds )
    {
        if( !is_array( $builds ) || empty( $builds ) )
            return false;

        $logs = array();

        $maxExecutionTime = 900;
        @ini_set("max_execution_time", $maxExecutionTime);
        set_time_limit($maxExecutionTime);

        Cache::disable();

        foreach( $builds as $build )
        {
            ob_start();

            try
            {
                if(!$this->dryRun)
                {
                    //trigger script
                    include( $build['script'] );

                    //trigger sql update
                    $this->executeSQL( $build['query'] );

                    //update config
                    $this->updateCoreShopBuild( (int) $build['build'] );
                }
            }
            catch (\Exception $e)
            {
                \Logger::error($e);
            }

            $logs[] = array($build['build'], $this->dryRun ? '- dry run, no message - ' : ob_get_clean() );

            \Logger::info('CoreShop System Build implemented: ' . $build['build']);

        }

        return array(
            "log" => $logs,
            "success" => true
        );

    }

    private function executeSQL($fileName)
    {
        if( $fileName === FALSE || !is_file( $fileName ) )
            return FALSE;

        $setup = new Setup();
        $setup->insertDump( $fileName );

        return TRUE;

    }

    private function cleanUp( )
    {
        \Pimcore\Cache::clearAll();

        $this->removeUpdateFolder();

    }

    public function removeUpdateFolder()
    {
        if( is_dir( CORESHOP_UPDATE_DIRECTORY ) )
        {
            recursiveDelete( CORESHOP_UPDATE_DIRECTORY, true);
        }

    }

}