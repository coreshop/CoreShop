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

namespace CoreShop\Plugin;

use Pimcore\Model\Tool\Setup;
use Pimcore\Cache;
use Pimcore\File;
use CoreShop\Version;
use CoreShop\Model\Configuration;

class Update {

    /**
     * @var bool
     */
    private $dryRun = FALSE;

    public function setDryRun($mode = TRUE)
    {
        $this->dryRun = $mode;

        return $this;
    }

    /**
     * Get available builds to install.
     * Mostly used by console command.
     * @return bool
     */
    public function getAvailableBuildList()
    {
        $buildState = $this->getBuildStatus();

        if ($buildState === FALSE)
        {
            return FALSE;
        }

        return $this->getBuilds($buildState['installed'], $buildState['newest']);
    }

    /**
     * @Todo: Check if in Backend && logged in user?
     * @return bool
     */
    public function updateCoreData()
    {
        $buildState = $this->getBuildStatus();

        if ($buildState === FALSE)
        {
            return FALSE;
        }

        $availableBuilds = $this->getBuilds($buildState['installed'], $buildState['newest']);

        $log = array();

        if (!empty($availableBuilds))
        {
            $execution = $this->executeBuildUpdates($availableBuilds);

            if ($execution['success'] == TRUE)
            {
                //clear cache and kill update folder.
                $this->cleanUp($buildState['newest']);

                array_merge($log, $execution['log']);
            }
        }

        $this->updateClasses();

        return array('success' => TRUE, 'log' => $log);
    }

    private function getBuildStatus()
    {
        $currentBuild = (int) Version::getBuildNumber();
        $installedBuild = Configuration::get("SYSTEM.BASE.BUILD");

        if ($currentBuild <= $installedBuild)
        {
            return FALSE;
        }

        return array(
            'newest' => (int) $currentBuild,
            'installed' => (int) $installedBuild
        );
    }

    private function updateCoreShopBuild($toBuild = 0)
    {
        return Configuration::set("SYSTEM.BASE.BUILD", $toBuild);
    }

    private function getBuilds($fromBuild = 0, $toBuild = 0)
    {
        if ($toBuild < $fromBuild)
        {
            return FALSE;
        }

        $builds = array();

        $newBuild = $fromBuild;

        while ($newBuild < $toBuild)
        {
            $newBuild++;

            $buildDir = CORESHOP_UPDATE_DIRECTORY . "/" . $newBuild;

            if (!is_dir($buildDir))
            {
                continue;
            }

            $scriptFile = $buildDir . "/postupdate.php";
            $QueryFile = $buildDir . "/query.sql";

            if (!is_file($scriptFile))
            {
                continue;
            }

            $builds[] = array(
                'build' => $newBuild,
                'script' => $scriptFile,
                'query' => is_file($QueryFile) ? $QueryFile : FALSE,
            );
        }

        return $builds;
    }

    private function executeBuildUpdates($builds)
    {
        if (!is_array($builds) || empty($builds))
        {
            return FALSE;
        }

        $logs = array();

        $maxExecutionTime = 900;
        @ini_set("max_execution_time", $maxExecutionTime);
        set_time_limit($maxExecutionTime);

        Cache::disable();

        foreach ($builds as $build)
        {
            ob_start();

            try
            {
                if (!$this->dryRun)
                {
                    //trigger script
                    include($build['script']);

                    //trigger sql update
                    $this->executeSQL($build['query']);

                    //update config
                    $this->updateCoreShopBuild((int) $build['build']);
                }
            } catch (\Exception $e)
            {
                \Logger::error($e);
            }

            $logs[] = array(
                $build['build'],
                $this->dryRun ? '- dry run, no message - ' : ob_get_clean()
            );

            \Logger::info('CoreShop System Build implemented: ' . $build['build']);
        }

        return array(
            "log" => $logs,
            "success" => TRUE
        );
    }

    private function executeSQL($fileName)
    {
        if ($fileName === FALSE || !is_file($fileName))
        {
            return FALSE;
        }

        if (filesize(($fileName)) > 0)
        {
            $setup = new Setup();
            $setup->insertDump($fileName);
        }

        return TRUE;
    }

    private function cleanUp()
    {
        \Pimcore\Cache::clearAll();

        $this->removeUpdateFolder();
    }

    /**
     * Remove Update Folder after Update.
     * @fixme: ugly idea, can we do that better?
     */
    public function removeUpdateFolder()
    {
        //Do not clean up, since the files are also going to be removed from git
        if (is_dir(CORESHOP_UPDATE_DIRECTORY))
        {
            //recursiveDelete( CORESHOP_UPDATE_DIRECTORY, true);
        }
    }

    /**
     * In ci-mode, we can't add the classes, because of the missing root user.
     * @fixme
     * @return bool
     */
    private function updateClasses()
    {
        if (!\Zend_Registry::isRegistered("pimcore_admin_user"))
        {
            return FALSE;
        }

        $classes = glob(PIMCORE_PLUGINS_PATH . '/CoreShop/install/class-*.json');

        if (!$this->dryRun && !empty($classes) && is_array($classes))
        {
            $install = new Install();
            foreach ($classes as $class)
            {
                $name = str_replace('class-', '', basename($class, '.json'));
                $install->createClass($name, TRUE);
            }
        }

        return TRUE;
    }

    /**
     * @return bool
     */
    public function isWriteable()
    {
        if ($this->dryRun)
        {
            return TRUE;
        }

        // check permissions
        $files = rscandir(CORESHOP_PATH . "/");
        foreach ($files as $file)
        {
            if (strpos($file, ".git") === FALSE)
            {
                if (!is_writable($file))
                {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    /**
     * https://api.github.com/repos/coreshop/CoreShop
     * @return mixed
     */
    public function getGitTagReleases()
    {
        $tagReleases = $this->gitRequest("https://api.github.com/repos/coreshop/CoreShop/tags");

        $releasesInfo = array();

        if (!empty($tagReleases))
        {

            foreach ($tagReleases as $release)
            {

                //check if version is already installed?
                $versionName = floatval( ltrim( $release->name, 'v') );

                $installedBuild = floatval( Configuration::get("SYSTEM.BASE.VERSION") );

                if( $installedBuild >= $versionName )
                    continue;

                $releaseInfo = array(
                    'sha' => $release->name,
                    'date' => '',
                    'message' => $release->commit->sha
                );

                $releasesInfo[] = $releaseInfo;
            }
        }

        return $releasesInfo;
    }

    public function getGitMasterCommit()
    {
        $master = $this->gitRequest("https://api.github.com/repos/coreshop/CoreShop/commits/master");

        if ($master === FALSE)
        {
            return FALSE;
        }

        $masterInfo = array();

        if (!empty($master))
        {
            $masterInfo[] = array(
                'sha' => $master->sha,
                'date' => $master->commit->committer->date,
                'message' => $master->commit->message . ' (#' . substr($master->sha, 0, 7) . ')'
            );
        }

        return $masterInfo;
    }

    public function installRelease($type, $release)
    {
        $coreShopPluginFolderName = 'CoreShop';

        $success = FALSE;
        $message = '';

        if ($type == 'update_master')
        {
            $url = 'https://api.github.com/repos/coreshop/CoreShop/zipball/master';
        }
        else
        {
            if ($type == 'update_releases')
            {
                $url = 'https://api.github.com/repos/coreshop/CoreShop/zipball/' . $release;
            }
        }

        $downloadDir = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/coreshop_update/';

        if (!is_dir($downloadDir))
        {
            File::mkdir($downloadDir);
        }

        $zipFile = $downloadDir . 'release.zip';
        $zipResource = fopen($zipFile, 'w');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);

        $content = curl_exec($ch);

        if( $content !== FALSE )
        {
            $zip = new \ZipArchive;

            if($zip->open($zipFile) === TRUE )
            {
                $zip->extractTo( $downloadDir );
                $zip->close();

                //delete Zip.
                unlink( $zipFile );

                $coreShopFolder = glob( $downloadDir . 'coreshop-CoreShop*', GLOB_ONLYDIR);

                //all done. move folder!
                if( !empty( $coreShopFolder ) && isset( $coreShopFolder[0]))
                {

                    //delete old CoreShop Plugin!
                    if( is_dir( PIMCORE_PLUGINS_PATH . '/' . $coreShopPluginFolderName ) )
                    {
                        //Backup .git first, if available!
                        $baseGitDir = PIMCORE_PLUGINS_PATH . '/' . $coreShopPluginFolderName . '/.git';

                        if( is_dir( $baseGitDir ) )
                        {
                            recursiveCopy( $baseGitDir, $coreShopFolder[0] . '/.git');
                        }

                        recursiveDelete( PIMCORE_PLUGINS_PATH . '/' . $coreShopPluginFolderName, true);

                    }

                    rename( $coreShopFolder[0], PIMCORE_PLUGINS_PATH . '/' . $coreShopPluginFolderName);

                    //now start default system update scripts!
                    $this->updateCoreData();

                    $success = TRUE;

                } else {

                    $message = 'No valid CoreShop Plugin Repo found in ' . $downloadDir;
                }

            } else {

                $message = 'Error while unpacking zip in ' . $downloadDir;
            }

        } else {

            $message = curl_error($ch);

        }

        curl_close($ch);

        return array('success' => $success, 'message' => $message);

    }

    /**
     * @param $url
     *
     * @return bool|array
     */
    public function gitRequest($url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $cont = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        if ($info['http_code'] == 200)
        {
            return json_decode($cont);
        }

        return FALSE;
    }
}
