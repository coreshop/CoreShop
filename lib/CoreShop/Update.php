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

namespace CoreShop;

use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\File;
use Pimcore\Tool;

class Update {

    /**
     * @var string
     */
    public static $tmpTable = "_coreshop_tmp_update";

    /**
     * Github Repo
     *
     * @var string
     */
    public static $githubRepo = "https://raw.githubusercontent.com/dpfaffenbauer/pimcore-coreshop";

    /**
     * Branch
     *
     * @var string
     */
    public static $branch = "Pimcore-4.0";


    /**
     *
     */
    public static function getAvailableUpdates() {
        $newerBuilds = self::getNewerBuilds();

        return array("revisions" => $newerBuilds, "releases" => array());
    }

    /**
     * @return bool
     */
    public static function isWriteable ()
    {
        // check permissions
        $files = rscandir(CORESHOP_PATH . "/");

        foreach ($files as $file) {
            if(strpos($file, ".git") === false) {
                if (!is_writable($file)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $toBuild
     * @return array
     */
    public static function getJobs($toBuild) {
        $builds = self::getNewerBuilds($toBuild);

        $updateScripts = array();
        $revisions = array();

        foreach($builds as $build) {
            $buildNumber = (string)$build['number'];

            $changedFiles = self::getChangedFilesForBuild($build['number']);
            $preUpdateScript = self::getScriptForBuild($build['number'], "preupdate");
            $postUpdateScript = self::getScriptForBuild($build['number'], "postupdate");

            foreach ($changedFiles as $download) {
                $jobs["parallel"][] = array(
                    "type" => "download",
                    "revision" => $buildNumber,
                    "file" => "file",
                    "url" => $download
                );

                $revisions[] = (int)$buildNumber;
            }

            if($preUpdateScript) {
                $updateScripts[$buildNumber]["preupdate"] = array(
                    "type" => "preupdate",
                    "revision" => $buildNumber
                );

                $jobs["parallel"][] = array(
                    "type" => "download",
                    "revision" => $buildNumber,
                    "file" => "script",
                    "url" => "preupdate.php"
                );

                $revisions[] = (int)$buildNumber;
            }

            if($postUpdateScript) {
                $updateScripts[$buildNumber]["postupdate"] = array(
                    "type" => "postupdate",
                    "revision" => $buildNumber
                );

                $jobs["parallel"][] = array(
                    "type" => "download",
                    "revision" => $buildNumber,
                    "file" => "script",
                    "url" => "postupdate.php"
                );

                $revisions[] = (int)$buildNumber;
            }
        }

        $revisions = array_unique($revisions);

        foreach ($revisions as $revision) {
            if($updateScripts[$revision]["preupdate"]) {
                $jobs["procedural"][] = $updateScripts[$revision]["preupdate"];
            }

            $jobs["procedural"][] = array(
                "type" => "files",
                "revision" => (string)$revision
            );


            if($updateScripts[$revision]["postupdate"]) {
                $jobs["procedural"][] = $updateScripts[$revision]["postupdate"];
            }
        }

        $jobs["procedural"][] = array(
            "type" => "clearcache"
        );

        $jobs["procedural"][] = array(
            "type" => "cleanup"
        );

        return $jobs;
    }

    /**
     * @param $revision
     * @param $url
     * @throws \Zend_Db_Adapter_Exception
     */
    public static function downloadData ($revision, $url, $fileType) {

        $downloadDir = PIMCORE_SYSTEM_TEMP_DIRECTORY . "/coreshop_update/" . $revision;

        $db = Db::get();

        $db->query("CREATE TABLE IF NOT EXISTS `" . self::$tmpTable . "` (
          `revision` int(11) NULL DEFAULT NULL,
          `path` varchar(255) NULL DEFAULT NULL
        );");

        if(!is_dir($downloadDir)) {
            File::mkdir($downloadDir);
        }

        $filesDir = $downloadDir . "/files/";
        if(!is_dir($filesDir)) {
            File::mkdir($filesDir);
        }

        $scriptsDir = $downloadDir . "/scripts/";
        if(!is_dir($scriptsDir)) {
            File::mkdir($scriptsDir);
        }

        $baseUrl = self::getRepoUrl();

        if($fileType === "file") {
            $baseUrl .= "/build/$revision/files/";
        }
        else if($fileType === "script") {
            $baseUrl .= "/build/$revision/scripts/";
        }

        $file = file_get_contents($baseUrl . $url);

        if($file)
        {
            if($fileType == "file")
            {
                $newFile = $filesDir . $url;
                File::put($newFile, $file);

                $db->insert(self::$tmpTable, array(
                    "revision" => $revision,
                    "path" => $url
                ));
            }
            else if ($fileType == "script")
            {
                $newScript = $scriptsDir. $url;
                File::put($newScript, $file);
            }
        }
    }

    /**
     * @param $revision
     */
    public static function installData ($revision) {

        $db = Db::get();
        $files = $db->fetchAll("SELECT * FROM `" . self::$tmpTable . "` WHERE revision = ?", $revision);

        foreach ($files as $file) {
            $path = pathinfo($file['path']);

            if (!is_dir(dirname(CORESHOP_PATH . $path["dirname"]))) {
                File::mkdir(dirname(CORESHOP_PATH . $path["dirname"]));
            }

            $srcFile = PIMCORE_SYSTEM_TEMP_DIRECTORY . "/coreshop_update/" . $revision . "/files/" . $file['path'];
            $destFile = CORESHOP_PATH . $file["path"];

            copy($srcFile, $destFile);
        }
    }

    /**
     * @param $revision
     * @param $type
     * @return array
     */
    public static function executeScript ($revision, $type) {

        $script = PIMCORE_SYSTEM_TEMP_DIRECTORY . "/coreshop_update/" . $revision . "/scripts/" . $type . ".php";
        $outputMessage = "";

        $maxExecutionTime = 900;
        @ini_set("max_execution_time", $maxExecutionTime);
        set_time_limit($maxExecutionTime);

        Cache::disable();

        if(is_file($script))
        {
            ob_start();
            try {
                include($script);
            }
            catch (\Exception $e) {
                \Logger::error($e);
            }
            $outputMessage = ob_get_clean();
        }

        return array(
            "message" => $outputMessage,
            "success" => true
        );
    }

    /**
     *
     */
    public static function cleanup () {

        // remove database tmp table
        $db = Db::get();
        $db->query("DROP TABLE IF EXISTS `" . self::$tmpTable . "`");

        //delete tmp data
        recursiveDelete(PIMCORE_SYSTEM_TEMP_DIRECTORY . "/coreshop_update", true);
    }

    /**
     * @param $build
     * @return null|string
     */
    public static function getScriptForBuild($build, $name) {
        try {
            $updateScript = file_get_contents(self::getRepoUrl() . "/build/" . $build . "/scripts/" . $name . ".php");

            if($updateScript) {
                return $updateScript;
            }
        }
        catch(\Exception $ex) {
            return null;
        }
    }

    /**
     * @param $build
     * @return array|null
     */
    public static function getChangedFilesForBuild($build) {
        try {
            $changedFiles = file_get_contents(self::getChangedFilesFileForBuild($build));

            if($changedFiles) {
                return explode(PHP_EOL, $changedFiles);
            }
        }
        catch(\Exception $ex) {
            return null;
        }
    }

    /**
     *
     */
    public static function getNewerBuilds($to = null) {
        $builds = self::getBuildsFile();

        if(is_array($builds)) {
            $pluginVersion = intval(Version::getBuildNumber());
            $newerBuilds = array();

            if(array_key_exists("builds", $builds)) {
                foreach ($builds['builds'] as $build) {
                    if ($build['number'] > $pluginVersion) {
                        if(!is_null($to)) {
                            if($to <= $build['number'])
                                $newerBuilds[] = $build;
                        }
                        else {
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
     * @param $buildNumber
     * @return string
     */
    public static function getChangedFilesFileForBuild($build) {
        return self::getRepoUrl() . "/build/$build/changedFiles.txt";
    }

    /**
     * Get Build File from Repo
     *
     * @return null|array
     */
    public static function getBuildsFile() {
        try {
            $builds = file_get_contents(self::getRepoUrl() . "/build/builds.json");

            if($builds) {
                $builds = \Zend_Json::decode($builds);

                return $builds;
            }
        }
        catch(\Exception $ex) {
            return null;
        }
    }

    /**
     * @return string
     */
    public static function getRepoUrl() {
        return self::$githubRepo . (self::$branch ? "/" . self::$branch : "");
    }
}