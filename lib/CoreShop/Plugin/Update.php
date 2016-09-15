<?php
/**
 * CoreShop
 * LICENSE
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Plugin;

use Pimcore\Logger;
use Pimcore\Model\Tool\Setup;
use Pimcore\Cache;
use Pimcore\File;
use CoreShop\Version;
use CoreShop\Model\Configuration;

/**
 * Class Update
 * @package CoreShop\Plugin
 */
class Update
{
    /**
     * Dry Run.
     *
     * @var bool
     */
    private $dryRun = false;

    /**
     * set Dry run.
     *
     * @param bool $mode
     *
     * @return $this
     */
    public function setDryRun($mode = true)
    {
        $this->dryRun = $mode;

        return $this;
    }

    /**
     * Get available builds to install.
     * Mostly used by console command.
     *
     * @return bool
     */
    public function getAvailableBuildList()
    {
        $buildState = $this->getBuildStatus();

        if ($buildState === false) {
            return false;
        }

        return $this->getBuilds($buildState['installed'], $buildState['newest']);
    }

    /**
     * update core data.
     *
     * @Todo: Check if in Backend && logged in user?
     *
     * @return bool
     */
    public function updateCoreData()
    {
        $buildState = $this->getBuildStatus();

        if ($buildState === false) {
            return false;
        }

        $availableBuilds = $this->getBuilds($buildState['installed'], $buildState['newest']);

        $log = array();

        if (!empty($availableBuilds)) {
            $execution = $this->executeBuildUpdates($availableBuilds);

            if ($execution['success'] == true) {
                //clear cache and kill update folder.
                $this->cleanUp($buildState['newest']);

                array_merge($log, $execution['log']);
            }
        }

        $this->updateClasses();

        return array('success' => true, 'log' => $log);
    }

    /**
     * get build status.
     *
     * @return array|bool
     */
    private function getBuildStatus()
    {
        $currentBuild = (int) Version::getBuildNumber();
        $installedBuild = Configuration::get('SYSTEM.BASE.BUILD');

        if ($currentBuild <= $installedBuild) {
            return false;
        }

        return array(
            'newest' => (int) $currentBuild,
            'installed' => (int) $installedBuild,
        );
    }

    /**
     * update core shop build.
     *
     * @param int $toBuild
     */
    private function updateCoreShopBuild($toBuild = 0)
    {
        return Configuration::set('SYSTEM.BASE.BUILD', $toBuild);
    }

    /**
     * get available builds.
     *
     * @param int $fromBuild
     * @param int $toBuild
     *
     * @return array|bool
     */
    private function getBuilds($fromBuild = 0, $toBuild = 0)
    {
        if ($toBuild < $fromBuild) {
            return false;
        }

        $builds = array();

        $newBuild = $fromBuild;

        while ($newBuild < $toBuild) {
            ++$newBuild;

            $buildDir = CORESHOP_UPDATE_DIRECTORY.'/'.$newBuild;

            if (!is_dir($buildDir)) {
                continue;
            }

            $scriptFile = $buildDir.'/postupdate.php';
            $QueryFile = $buildDir.'/query.sql';

            if (!is_file($scriptFile)) {
                continue;
            }

            $builds[] = array(
                'build' => $newBuild,
                'script' => $scriptFile,
                'query' => is_file($QueryFile) ? $QueryFile : false,
            );
        }

        return $builds;
    }

    /**
     * Execute build updates.
     *
     * @param $builds
     *
     * @return array|bool
     */
    private function executeBuildUpdates($builds)
    {
        if (!is_array($builds) || empty($builds)) {
            return false;
        }

        $logs = array();

        $maxExecutionTime = 900;
        @ini_set('max_execution_time', $maxExecutionTime);
        set_time_limit($maxExecutionTime);

        Cache::disable();

        foreach ($builds as $build) {
            ob_start();

            try {
                if (!$this->dryRun) {
                    //trigger script
                    include $build['script'];

                    //trigger sql update
                    $this->executeSQL($build['query']);

                    //update config
                    $this->updateCoreShopBuild((int) $build['build']);
                }
            } catch (\Exception $e) {
                Logger::error($e);
            }

            $logs[] = array(
                $build['build'],
                $this->dryRun ? '- dry run, no message - ' : ob_get_clean(),
            );

            Logger::info('CoreShop System Build implemented: '.$build['build']);
        }

        return array(
            'log' => $logs,
            'success' => true,
        );
    }

    /**
     * execute sql.
     *
     * @param $fileName
     *
     * @return bool
     */
    private function executeSQL($fileName)
    {
        if ($fileName === false || !is_file($fileName)) {
            return false;
        }

        if (filesize(($fileName)) > 0) {
            $setup = new Setup();
            $setup->insertDump($fileName);
        }

        return true;
    }

    /**
     * Clean up.
     */
    private function cleanUp()
    {
        \Pimcore\Cache::clearAll();

        $this->removeUpdateFolder();
    }

    /**
     * Remove Update Folder after Update.
     *
     * @fixme: ugly idea, can we do that better?
     */
    public function removeUpdateFolder()
    {
        //Do not clean up, since the files are also going to be removed from git
        if (is_dir(CORESHOP_UPDATE_DIRECTORY)) {
            //recursiveDelete( CORESHOP_UPDATE_DIRECTORY, true);
        }
    }

    /**
     * @return bool
     */
    private function updateClasses()
    {
        $classes = glob(PIMCORE_PLUGINS_PATH.'/CoreShop/install/class-*.json');

        if (!$this->dryRun && !empty($classes) && is_array($classes)) {
            $install = new Install();
            foreach ($classes as $class) {
                $name = str_replace('class-', '', basename($class, '.json'));
                $install->createClass($name, true);
            }
        }

        return true;
    }

    /**
     * Check if CoreShop Path is writeable.
     *
     * @return bool
     */
    public function isWriteable()
    {
        if ($this->dryRun) {
            return true;
        }

        // check permissions
        $files = rscandir(CORESHOP_PATH.'/');
        foreach ($files as $file) {
            if (strpos($file, '.git') === false) {
                if (!is_writable($file)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * get git tags.
     *
     * https://api.github.com/repos/coreshop/CoreShop
     *
     * @return mixed
     */
    public function getGitTagReleases()
    {
        $tagReleases = $this->gitRequest('https://api.github.com/repos/coreshop/CoreShop/tags');

        $releasesInfo = array();

        if (!empty($tagReleases)) {
            foreach ($tagReleases as $release) {

                //check if version is already installed?
                $versionName = floatval(ltrim($release->name, 'v'));

                $installedBuild = floatval(Configuration::get('SYSTEM.BASE.VERSION'));

                if ($installedBuild >= $versionName) {
                    continue;
                }

                $releaseInfo = array(
                    'sha' => $release->name,
                    'date' => '',
                    'message' => $release->commit->sha,
                );

                $releasesInfo[] = $releaseInfo;
            }
        }

        return $releasesInfo;
    }

    /**
     * get git master commit.
     *
     * @return array|bool
     */
    public function getGitMasterCommit()
    {
        $master = $this->gitRequest('https://api.github.com/repos/coreshop/CoreShop/commits/master');

        if ($master === false) {
            return false;
        }

        $masterInfo = array();

        if (!empty($master)) {
            $installedSha = Configuration::get('SYSTEM.BASE.COMMITSHA');

            //master is head.
            if (is_null($installedSha) || $installedSha !== $master->sha) {
                $masterInfo[] = array(
                    'sha' => $master->sha,
                    'date' => $master->commit->committer->date,
                    'message' => $master->commit->message.' (#'.substr($master->sha, 0, 7).')',
                );
            }
        }

        return $masterInfo;
    }

    /**
     * install release version.
     *
     * @param $type
     * @param $release
     *
     * @return array
     */
    public function installRelease($type, $release)
    {
        $coreShopPluginFolderName = 'CoreShop';

        $success = false;
        $message = '';

        if ($type == 'update_master') {
            $url = 'https://api.github.com/repos/coreshop/CoreShop/zipball/master';
        } else {
            if ($type == 'update_releases') {
                $url = 'https://api.github.com/repos/coreshop/CoreShop/zipball/'.$release;
            }
        }

        $downloadDir = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update/';

        if (!is_dir($downloadDir)) {
            File::mkdir($downloadDir);
        }

        $zipFile = $downloadDir.'release.zip';
        $zipResource = fopen($zipFile, 'w');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15'));

        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);

        $content = curl_exec($ch);

        if ($content !== false) {
            $zip = new \ZipArchive();

            if ($zip->open($zipFile) === true) {
                $zip->extractTo($downloadDir);
                $zip->close();

                //delete Zip.
                unlink($zipFile);

                $coreShopFolder = glob($downloadDir.'coreshop-CoreShop*', GLOB_ONLYDIR);

                //all done. move folder!
                if (!empty($coreShopFolder) && isset($coreShopFolder[0])) {
                    $hasGit = false;

                    //delete old CoreShop Plugin!
                    if (is_dir(PIMCORE_PLUGINS_PATH.'/'.$coreShopPluginFolderName)) {
                        $hasGit = is_dir(PIMCORE_PLUGINS_PATH.'/'.$coreShopPluginFolderName.'/.git');
                        recursiveDelete(PIMCORE_PLUGINS_PATH.'/'.$coreShopPluginFolderName, true);
                    }

                    rename($coreShopFolder[0], PIMCORE_PLUGINS_PATH.'/'.$coreShopPluginFolderName);

                    //now start default system update scripts!
                    $this->updateCoreData();

                    //@todo if $hasGit is true, trigger git pull to fetch latest

                    //if type is master, save commitsha in config
                    if ($type == 'update_master') {
                        Configuration::set('SYSTEM.BASE.COMMITSHA', $release);
                    }

                    $success = true;
                } else {
                    $message = 'No valid CoreShop Plugin Repo found in '.$downloadDir;
                }
            } else {
                $message = 'Error while unpacking zip in '.$downloadDir;
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
    private function gitRequest($url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15'));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $cont = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        if ($info['http_code'] == 200) {
            return json_decode($cont);
        }

        return false;
    }
}
