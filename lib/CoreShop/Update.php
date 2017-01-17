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

namespace CoreShop;

use CoreShop\Plugin\Install;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\File;
use Pimcore\Logger;

/**
 * Class Update
 * @package CoreShop
 */
class Update
{
    /**
     * Temp Table Name.
     *
     * @var string
     */
    public static $tmpTable = '_coreshop_tmp_update';

    /**
     * Github Repo.
     *
     * @var string
     */
    public static $buildServerInfo = 'https://update.coreshop.org/builder/web/build/info';

    /**
     * Github Repo.
     *
     * @var string
     */
    public static $buildServerData = 'https://update.coreshop.org/builder/web/builds';

    /**
     * Dry run.
     *
     * @var bool
     */
    public static $dryRun = false;

    /**
     *  Check for Updates.
     *
     * @param $currentBuild
     *
     * @return array
     */
    public static function getAvailableUpdates($currentBuild = null)
    {
        if (!$currentBuild) {
            $currentBuild = Version::getBuildNumber();
        }

        $newerBuilds = self::getNewerBuilds($currentBuild);

        return ['revisions' => $newerBuilds, 'releases' => []];
    }

    /**
     * Check if CoreShop Path is writeable.
     *
     * @return bool
     */
    public static function isWriteable()
    {
        if (self::$dryRun) {
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
     * get jobs to build.
     *
     * @param $toBuild
     * @param $currentBuild
     *
     * @return array
     */
    public static function getPackages($toBuild, $currentBuild = null)
    {
        if (!$currentBuild) {
            $currentBuild = Version::getBuildNumber();
        }

        $builds = self::getNewerBuilds($currentBuild, $toBuild);

        $jobs = [];

        if (is_array($builds)) {
            foreach ($builds as $build) {
                $buildNumber = (string)$build['number'];
                $buildPackage = self::$buildServerData . '/' . $buildNumber . '.zip';

                //@fixme: check if package is available!
                $jobs['parallel'][] = [
                    'type' => 'download',
                    'revision' => $buildNumber,
                    'file' => 'file',
                    'url' => $buildPackage,
                ];
            }
        }

        return $jobs;
    }

    /**
     * get jobs to build.
     *
     * @param $toBuild
     * @param $currentBuild
     *
     * @return array
     */
    public static function getJobs($toBuild, $currentBuild = null)
    {
        if (!$currentBuild) {
            $currentBuild = Version::getBuildNumber();
        }

        $db = Db::get();
        $db->query('CREATE TABLE IF NOT EXISTS `'.self::$tmpTable.'` (
          `revision` int(11) NULL DEFAULT NULL,
          `path` varchar(255) NULL DEFAULT NULL
        );');

        $builds = self::getNewerBuilds($currentBuild, $toBuild);

        $updateScripts = [];
        $revisions = [];
        $jobs = [
            'parallel' => [

            ]
        ];

        if (is_array($builds)) {
            foreach ($builds as $build) {
                $buildNumber = (string)$build['number'];

                $changedFiles = self::getChangedFilesForBuild($build['number']);
                $preUpdateScript = self::getScriptForBuild($build['number'], 'preupdate');
                $postUpdateScript = self::getScriptForBuild($build['number'], 'postupdate');

                $updateScripts[$buildNumber] = [];

                if (is_array($changedFiles)) {
                    foreach ($changedFiles as $download) {
                        if (empty($download)) {
                            continue;
                        }

                        $jobs['parallel'][] = [
                            'type' => 'arrange',
                            'revision' => $buildNumber,
                            'file' => 'file',
                            'url' => $download . '.build',
                        ];

                        if (strpos($download, 'install/class-') === 0) {
                            if (!is_array($updateScripts[$buildNumber]['installClass'])) {
                                $updateScripts[$buildNumber]['installClass'] = [];
                            }

                            $updateScripts[$buildNumber]['installClass'][] = [
                                'type' => 'installClass',
                                'revision' => $buildNumber,
                                'class' => str_replace('.json', '', str_replace('install/class-', '', $download)),
                            ];
                        }

                        if (strpos($download, 'install/translations/admin.csv') === 0) {
                            $updateScripts[$buildNumber]['importTranslation'] = [
                                'type' => 'importTranslations',
                                'revision' => $buildNumber,
                            ];
                        }

                        $revisions[] = (int)$buildNumber;
                    }
                }

                if (!is_null($preUpdateScript)) {
                    $updateScripts[$buildNumber]['preupdate'] = [
                        'type' => 'preupdate',
                        'revision' => $buildNumber,
                    ];

                    $jobs['parallel'][] = [
                        'type' => 'arrange',
                        'revision' => $buildNumber,
                        'file' => 'script',
                        'url' => 'preupdate.php',
                    ];

                    $revisions[] = (int)$buildNumber;
                }

                if (!is_null($postUpdateScript)) {
                    $updateScripts[$buildNumber]['postupdate'] = [
                        'type' => 'postupdate',
                        'revision' => $buildNumber,
                    ];

                    $jobs['parallel'][] = [
                        'type' => 'arrange',
                        'revision' => $buildNumber,
                        'file' => 'script',
                        'url' => 'postupdate.php',
                    ];

                    $revisions[] = (int)$buildNumber;
                }
            }
        }

        $revisions = array_unique($revisions);

        foreach ($revisions as $revision) {
            if ($updateScripts[$revision]['preupdate']) {
                $jobs['procedural'][] = $updateScripts[$revision]['preupdate'];
            }

            $jobs['procedural'][] = [
                'type' => 'files',
                'revision' => (string) $revision,
            ];

            if (is_array($updateScripts[$revision]['installClass'])) {
                foreach ($updateScripts[$revision]['installClass'] as $installClass) {
                    $jobs['procedural'][] = $installClass;
                }
            }

            if ($updateScripts[$revision]['importTranslation']) {
                $jobs['procedural'][] = $updateScripts[$revision]['importTranslation'];
            }

            $deletedFiles = self::getDeletedFilesForBuild($revision);

            if ($deletedFiles) {
                foreach ($deletedFiles as $toDelete) {
                    if ($toDelete) {
                        $jobs['procedural'][] = [
                            'type' => 'deleteFile',
                            'url' => $toDelete,
                        ];
                    }
                }
            }

            if ($updateScripts[$revision]['postupdate']) {
                $jobs['procedural'][] = $updateScripts[$revision]['postupdate'];
            }
        }

        $jobs['procedural'][] = [
            'type' => 'clearcache',
        ];

        $jobs['procedural'][] = [
            'type' => 'cleanup',
        ];

        return $jobs;
    }

    /**
     * Delete file.
     *
     * @param $file
     */
    public static function deleteData($file)
    {
        $file = CORESHOP_PATH.'/'.$file;

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @param $revision
     * @param $url
     *
     * @throws Exception
     */
    public static function downloadPackage($revision, $url)
    {
        $downloadDir = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update/'.$revision;

        if (!is_dir($downloadDir)) {
            File::mkdir($downloadDir);
        }

        $zipFile = $downloadDir.basename($url);

        try {
            $zipResource = fopen($zipFile, 'w');
        } catch (\Exception $e) {
            throw new Exception('Error while downloading package: '.$e->getMessage());
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15']);

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
            } else {
                throw new Exception('Error while extracting package: '.$zipFile);
            }
        } else {
            throw new Exception('Error while downloading package: '.curl_error($ch));
        }

        curl_close($ch);
    }

    /**
     * @param $revision
     * @param $url
     * @param $fileType
     *
     * @throws Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public static function arrangeData($revision, $url, $fileType)
    {
        $db = Db::get();

        $downloadDir = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update';
        $baseDir = $downloadDir;

        if ($fileType === 'file') {
            $baseDir .= "/$revision/files/";
        } elseif ($fileType === 'script') {
            $baseDir .= "/$revision/scripts/";
        }

        if (file_exists($baseDir.$url)) {
            if ($fileType == 'file') {
                $db->insert(self::$tmpTable, [
                    'revision' => $revision,
                    'path' => $url,
                ]);
            }
        } else {
            throw new Exception('Install file (ref '.$revision.') not found: '.$url);
        }
    }

    /**
     * install data.
     *
     * @param $revision
     */
    public static function installData($revision)
    {
        $db = Db::get();
        $files = $db->fetchAll('SELECT * FROM `'.self::$tmpTable.'` WHERE revision = ?', $revision);

        foreach ($files as $file) {
            $path = pathinfo($file['path']);

            if (!self::$dryRun) {
                if (!is_dir(CORESHOP_PATH.'/'.$path['dirname'])) {
                    File::mkdir(CORESHOP_PATH.'/'.$path['dirname']);
                }
            }

            $srcFile = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update/'.$revision.'/files/'.$file['path'];
            $destFile = CORESHOP_PATH.'/'.substr($file['path'], 0, -1 * strlen('.build'));

            if (!self::$dryRun) {
                copy($srcFile, $destFile);
            }
        }
    }

    /**
     * execute script.
     *
     * @param $revision
     * @param string $type
     *
     * @return array
     */
    public static function executeScript($revision, $type)
    {
        $script = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update/'.$revision.'/scripts/'.$type.'.php';
        $outputMessage = '';

        $maxExecutionTime = 900;
        @ini_set('max_execution_time', $maxExecutionTime);
        set_time_limit($maxExecutionTime);

        Cache::disable();

        if (is_file($script)) {
            ob_start();
            try {
                if (!self::$dryRun) {
                    include $script;
                }
            } catch (\Exception $e) {
                \Pimcore\Logger::error($e);
            }
            $outputMessage = ob_get_clean();
        }

        return [
            'message' => $outputMessage,
            'success' => true,
        ];
    }

    /**
     * install class.
     *
     * @param $class
     *
     * @return array
     */
    public static function installClass($class)
    {
        if (!self::$dryRun) {
            try {
                $install = new Install();
                $install->createClass($class, true);
            } catch (\Exception $e) {
                \Pimcore\Logger::err('CoreShop installClass error (' . $class . '): ' . $e->getMessage());
            }
        }

        return [
            'message' => 'Installed Class ' . $class,
            'success' => true,
        ];
    }

    /**
     * make some cleanup.
     */
    public static function cleanup()
    {

        // remove database tmp table
        $db = Db::get();
        $db->query('DROP TABLE IF EXISTS `'.self::$tmpTable.'`');

        //delete tmp data
        recursiveDelete(PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update', true);
    }

    /**
     * get scripts for build.
     *
     * @param $build
     * @param string $name
     *
     * @return null|string
     */
    public static function getScriptForBuild($build, $name)
    {
        $path = PIMCORE_SYSTEM_TEMP_DIRECTORY.'/coreshop_update/'.$build.'/scripts/'.$name.'.php';

        try {
            $updateScript = @file_get_contents($path);

            if ($updateScript) {
                return $updateScript;
            }
        } catch (\Exception $ex) {
            Logger::log(sprintf("Unable to get script for build: %s (%s)", $build, $path));
        }

        return null;
    }

    /**
     * get changed files for build.
     *
     * @param $build
     *
     * @return array|null
     */
    public static function getChangedFilesForBuild($build)
    {
        $path = self::getChangedFilesFileForBuild($build);

        try {
            $changedFiles = @file_get_contents($path);

            if ($changedFiles) {
                return explode(PHP_EOL, $changedFiles);
            }
        } catch (\Exception $ex) {
            Logger::info(sprintf("Couldn't load %s file", $path));
        }

        return null;
    }

    /**
     * get deleted files for build.
     *
     * @param $build
     *
     * @return array|null
     */
    public static function getDeletedFilesForBuild($build)
    {
        $path = self::getDeletedFilesFileForBuild($build);

        try {
            $changedFiles = @file_get_contents($path);

            if ($changedFiles) {
                return explode(PHP_EOL, $changedFiles);
            }
        } catch (\Exception $ex) {
            Logger::info(sprintf("Couldn't load %s file", $path));
        }

        return null;
    }

    /**
     * get newer builds.
     *
     * @param null $currentBuild
     * @param null $to
     *
     * @return array|bool
     */
    public static function getNewerBuilds($currentBuild = null, $to = null)
    {
        $builds = self::getBuildsFile();

        if (!$currentBuild) {
            $currentBuild = Version::getBuildNumber();
        }

        if (is_array($builds)) {
            $pluginVersion = intval($currentBuild);
            $newerBuilds = [];

            if (array_key_exists('builds', $builds)) {
                foreach ($builds['builds'] as $build) {
                    if ($build['number'] > $pluginVersion) {
                        if (!is_null($to)) {
                            if ($to >= $build['number']) {
                                $newerBuilds[] = $build;
                            }
                        } else {
                            $newerBuilds[] = $build;
                        }
                    }
                }
            }

            return $newerBuilds;
        }

        return false;
    }

    /**
     * get changed files for build.
     *
     * @param $build
     *
     * @return string
     */
    public static function getChangedFilesFileForBuild($build)
    {
        return PIMCORE_SYSTEM_TEMP_DIRECTORY."/coreshop_update/$build/changedFiles.txt";
    }

    /**
     * get deleted files for build.
     *
     * @param $build
     *
     * @return string
     */
    public static function getDeletedFilesFileForBuild($build)
    {
        return PIMCORE_SYSTEM_TEMP_DIRECTORY."/coreshop_update/$build/deletedFiles.txt";
    }

    /**
     * Get Build File from Repo.
     *
     * @return null|array
     */
    public static function getBuildsFile()
    {
        try {
            $builds = @file_get_contents(self::$buildServerInfo);

            if ($builds) {
                $builds = \Zend_Json::decode($builds);

                return $builds;
            }
        } catch (\Exception $ex) {
            Logger::info(sprintf("Couldn't load %s file", self::$buildServerInfo));
        }

        return null;
    }
}
