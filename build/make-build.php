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

$changedFiles = getChangedFiles();

$buildNumber = \CoreShop\Version::getBuildNumber();
$buildNumber = $buildNumber + 1;

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
        $sourceFile = __DIR__ . "/../" . $file;
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

    updateVersionFile($buildNumber);
}
else {
    echo "no files changed, no build will be created!" . PHP_EOL;
    exit;
}

unlink(CORESHOP_CHANGED_FILES);

/**
 * Update Plugin XML File
 *
 * @param $buildNumber
 * @throws Exception
 * @throws Zend_Config_Exception
 */
function updateVersionFile($buildNumber) {
    $config = \Pimcore\ExtensionManager::getPluginConfig("CoreShop");

    $config['plugin']['pluginRevision'] = $buildNumber;
    $config['plugin']['pluginBuildTimestamp'] = time();
    $config['plugin']['pluginGitRevision'] = getGitRevision();

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

