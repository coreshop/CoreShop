<?php

function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (is_dir($dir."/".$object))
           rrmdir($dir."/".$object);
         else
           unlink($dir."/".$object);
       }
     }
     rmdir($dir);
   }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!defined("CORESHOP_TESTS_PATH")) {
    define('CORESHOP_TESTS_PATH', realpath(dirname(__FILE__)));
}

if (!defined('PIMCORE_APP_ROOT')) {
    define('PIMCORE_APP_ROOT', __DIR__ . '/app');
}


define('PIMCORE_PRIVATE_VAR', CORESHOP_TESTS_PATH . "/tmp/var");

require_once '../../../pimcore/config/constants.php';
$loader = require_once PIMCORE_PATH . '/config/autoload.php';
include_once 'app/AppKernel.php';

$loader->add('CoreShop\Test', [__DIR__ . "/lib", __DIR__ . "/"]);

$phpLog = PIMCORE_LOG_DIRECTORY . '/php.log';
if (is_writable(PIMCORE_LOG_DIRECTORY)) {
    ini_set('error_log', $phpLog);
    ini_set('log_errors', '1');
}

$date = date('m/d/Y h:i:s a', time());
print($date . "\n");

// some general pimcore definition overwrites
define('PIMCORE_ORIG_PRIVATE_VAR', PIMCORE_PROJECT_ROOT . '/var/config');
define("PIMCORE_ADMIN", true);
define("PIMCORE_DEBUG", true);
define("PIMCORE_DEVMODE", true);

if (!file_exists(PIMCORE_PRIVATE_VAR)) {
    mkdir(PIMCORE_PRIVATE_VAR, 0777, true); //for first run
}

@mkdir(CORESHOP_TESTS_PATH . "/output", 0777, true);

// empty temporary var directory
rrmdir(PIMCORE_PRIVATE_VAR);
mkdir(PIMCORE_PRIVATE_VAR, 0777, true);
mkdir(PIMCORE_PRIVATE_VAR . "/cache", 0777, true);
mkdir(PIMCORE_PRIVATE_VAR . "/cache/dev", 0777, true);

// get default configuration for the test
$testConfig = include(CORESHOP_TESTS_PATH . '/config/testconfig.php');

// get configuration from main project
$systemConfigFile = realpath(PIMCORE_ORIG_PRIVATE_VAR . "/system.php");
$systemConfig = null;

if (is_file($systemConfigFile)) {
    $systemConfig = include $systemConfigFile;

    // this is to allow localhost tests
    $testConfig["rest"]["host"] = "pimcore-local-unittest";
}

try {
    // use the default db configuration if there's no main project (eg. travis automated builds)
    $dbConfig = $testConfig["database"];
    if (is_array($systemConfig) && array_key_exists("database", $systemConfig)) {
        // if there's a configuration for the main project, use that one and replace the database name
        $dbConfig = $systemConfig["database"];
        $dbConfig["params"]["dbname"] = $dbConfig["params"]["dbname"] . "___phpunit";

        // remove write only config
        if (isset($dbConfig["writeOnly"])) {
            unset($dbConfig["writeOnly"]);
        }
    }

    // use mysqli for that, because Zend_Db requires a DB for a connection
    $db = new \PDO('mysql:host=' . $dbConfig["params"]["host"] . ';port=' . (int) $dbConfig["params"]["port"] . ';', $dbConfig["params"]["username"], $dbConfig["params"]["password"]);
    $db->query("SET NAMES utf8");

    $db->query("DROP database IF EXISTS " . $dbConfig["params"]["dbname"] . ";");
    $db->query("CREATE DATABASE " . $dbConfig["params"]["dbname"] . " charset=utf8");
    $db = null;
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    die("Couldn't establish connection to mysql" . "\n");
}


if (defined('HHVM_VERSION')) {
    // always use PDO in hhvm environment (mysqli is not supported)
    $dbConfig["adapter"] = "Pdo_Mysql";
}

echo "\n\nDatabase Config: ". print_r($dbConfig, true) . "\n\n";

echo "\n\nInstalling Pimcore Config";
$setup = new \Pimcore\Model\Tool\Setup();
$setup->config([
    "database" => $dbConfig,
    "webservice" => ["enabled" => 1],
    "general" => ["validLanguages" => "en,de"]
]);

echo "\n\nBootstrap Kernel";
$kernel = new AppKernel('dev', true);
$kernel->boot();
Pimcore::setKernel($kernel);

\Pimcore::initConfiguration();

// force the db wrapper to use only one connection, regardless if read/write
if (is_array($systemConfig)) {
    $db = \Pimcore\Db::get();
}
echo "\n\nInstall Pimcore Database";
$setup->database();
$setup->contents([
    "username" => "admin",
    "password" => microtime()
]);

echo "\nSetup done...\n";

// to be sure => reset the database
\Pimcore\Db::reset();
\Pimcore\Cache::disable();

// add the tests, which still reside in the original development unit, not in pimcore_phpunit to the include path
$includePaths = [
    get_include_path()
];

//install CoreShop
//$install = new \CoreShop\Plugin\Install();
//$install->executeSQL('CoreShop');
//$install->executeSQL('CoreShop-States');
//$install->createConfig();
//$install->fullInstall();

//\CoreShop\Model\Product::$unitTests = true;

echo "\n\nInstall CoreShop";
/*$bundleManager = \Pimcore::getContainer()->get('pimcore.extension.bundle_manager');
$assetsInstaller = \Pimcore::getContainer()->get('pimcore.tool.assets_installer');
$bundleId = 'CoreShop\Bundle\AdminBundle\CoreShopAdminBundle';

$bundleManager->setState($bundleId, true);
$bundle = $bundleManager->getActiveBundle($bundleId);
$bundleManager->install($bundle);

try {
    $installProcess = $assetsInstaller->install();
} catch (\Symfony\Component\Process\Exception\ProcessFailedException $e) {
    throw $e;
}*/
$install = new \CoreShop\Bundle\AdminBundle\Pimcore\Install();
$install->install();

//\Pimcore\ExtensionManager::enable("plugin", "CoreShop");
//\Pimcore\API\Plugin\Broker::getInstance()->registerPlugin(new \CoreShop\Plugin());

//Zend_Session::$_unitTestEnabled = true;
//\Zend_Registry::set("Zend_Locale", new \Zend_Locale("en"));

//\CoreShop\Test\Data::createData();

/**
 * bootstrap is done, phpunit_pimcore is up and running.
 * It has a database, admin user and a complete config.
 * We can start running our tests against the phpunit_pimcore instance
 */
