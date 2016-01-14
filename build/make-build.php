<?php

/**
 * Creates a new build an increases the Build-Number
 */

include(__DIR__ . "/../../../pimcore/cli/startup.php");

if(!\Pimcore\Tool::classExists("\\CoreShop\\Version")) {
    //CoreShop was not loaded as Plugin, so we have to manually add the Autoloader path
    $config = \Pimcore\ExtensionManager::getPluginConfig("CoreShop");

    $autoloader = \Zend_Loader_Autoloader::getInstance();

    $includePaths = array(
        get_include_path()
    );

    if (is_array($config['plugin']['pluginIncludePaths']['path'])) {
        foreach ($config['plugin']['pluginIncludePaths']['path'] as $path) {
            $includePaths[] = PIMCORE_PLUGINS_PATH . $path;
        }
    }
    else if ($config['plugin']['pluginIncludePaths']['path'] != null) {
        $includePaths[] = PIMCORE_PLUGINS_PATH . $config['plugin']['pluginIncludePaths']['path'];
    }
    if (is_array($config['plugin']['pluginNamespaces']['namespace'])) {
        foreach ($config['plugin']['pluginNamespaces']['namespace'] as $namespace) {
            $autoloader->registerNamespace($namespace);
        }
    }
    else if ($config['plugin']['pluginNamespaces']['namespace'] != null) {
        $autoloader->registerNamespace($config['plugin']['pluginNamespaces']['namespace']);
    }

    set_include_path(implode(PATH_SEPARATOR, $includePaths));
}

include(__DIR__ . "/../config/startup.php");

if (!defined("CORESHOP_CHANGED_FILES")) define("CORESHOP_CHANGED_FILES", CORESHOP_BUILD_DIRECTORY . "/changedFiles.txt");

$version = \CoreShop\Version::getVersion();

$buildNumber = \CoreShop\Version::getBuildNumber();
$buildNumber = $buildNumber + 1;

$timestamp = time();
$gitRevision = getGitRevision();

$changedFiles = getChangedFiles();

if(count($changedFiles) > 0)
{
    $buildFolder = CORESHOP_BUILD_DIRECTORY . "/" . $buildNumber;
    $buildFolderScripts = $buildFolder . "/scripts";
    $buildFolderFiles = $buildFolder . "/files";

    if(!is_dir($buildFolder)) {
        mkdir($buildFolder);
    }

    if(!is_dir($buildFolderScripts)) {
        mkdir($buildFolderScripts);
    }

    if(!is_dir($buildFolderFiles)) {
        mkdir($buildFolderFiles);
    }


    //Copy all Files into files folder
    foreach ($changedFiles as $file) {
        $file = str_replace("\n", "", $file);
        $sourceFile = CORESHOP_PATH . "/" . $file;
        $destinationFile = $buildFolderFiles . "/" . $file;
        $pathInfo = pathinfo($destinationFile);

        if (!is_dir($pathInfo['dirname'])) {
            mkdir($pathInfo['dirname'], 0755, true);
        }

        if (file_exists($destinationFile)) {
            unlink($destinationFile);
        }

        echo "copy file " . $file . PHP_EOL;

        copy($sourceFile, $destinationFile);
    }

    rename(CORESHOP_CHANGED_FILES, CORESHOP_BUILD_DIRECTORY . "/" . $buildNumber . "/changedFiles.txt");

    updateVersionFile($buildNumber, $version, $timestamp, $gitRevision);
    writeBuildToBuildFile($buildNumber, $version, $timestamp, $gitRevision);
    gitAddAndCommit($buildNumber);
}
else {
    //delete "changedfiles" when no files has been changed
    unlink(CORESHOP_CHANGED_FILES);

    echo "no files changed, no build will be created!" . PHP_EOL;
    exit;
}

/**
 * Update Plugin XML File
 *
 * @param $buildNumber
 * @param $version
 * @param $timestamp
 * @param $gitRevision
 * @throws Exception
 * @throws Zend_Config_Exception
 */
function updateVersionFile($buildNumber, $version, $timestamp, $gitRevision) {
    $config = \Pimcore\ExtensionManager::getPluginConfig("CoreShop");

    $config['plugin']['pluginRevision'] = $buildNumber;
    $config['plugin']['pluginVersion'] = $version;
    $config['plugin']['pluginBuildTimestamp'] = $timestamp;
    $config['plugin']['pluginGitRevision'] = $gitRevision;

    $config = new \Zend_Config($config, true);
    $writer = new \Zend_Config_Writer_Xml(array(
        "config" => $config,
        "filename" => CORESHOP_PLUGIN_CONFIG
    ));
    $writer->write();
}

/**
 * @return string
 */
function getGitRevision() {
    return str_replace("\n", "", \Pimcore\Tool\Console::exec("git rev-parse HEAD"));
}

/**
 * @return array
 */
function getChangedFiles() {
    $gitDirectory = CORESHOP_PATH;
    $gitRevision = \CoreShop\Version::getGitRevision();

    if(!$gitRevision)
        $gitRevision = '383e78d';

    \Pimcore\Tool\Console::exec("git diff-tree -r --no-commit-id --name-only --diff-filter=ACMRT $gitRevision HEAD $gitDirectory", CORESHOP_CHANGED_FILES);

    return file(CORESHOP_CHANGED_FILES);
}

/**
 * add files to git and make a commit
 *
 * @param $buildNumber
 */
function gitAddAndCommit($buildNumber) {
    \Pimcore\Tool\Console::exec("git add plugin.xml; git add -f build/$buildNumber");
    //\Pimcore\Tool\Console::exec("git commit -m \"Build Version $buildNumber\"");
}

/**
 * write build to build file
 *
 * @param $buildNumber
 * @param $version
 * @param $timestamp
 * @param $gitRevision
 */
function writeBuildToBuildFile($buildNumber, $version, $timestamp, $gitRevision) {
    $buildsFile = file_get_contents(CORESHOP_BUILD_DIRECTORY . "/builds.json");

    try {
        $json = \Zend_Json::decode($buildsFile);
    }
    catch (\Exception $ex) {
        $json = array(
            "builds" => array()
        );
    }

    $json["builds"][] = array(
        "number" => $buildNumber,
        "version" => $version,
        "timestamp" => $timestamp,
        "gitRevision" => $gitRevision
    );

    $buildsFile = \Zend_Json::encode($json);

    file_put_contents(CORESHOP_BUILD_DIRECTORY . "/builds.json", $buildsFile);
}