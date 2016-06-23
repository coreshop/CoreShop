<?php
$date = date('m/d/Y h:i:s a', time());
print($date . "\n");

@ini_set("display_errors", "On");
@ini_set("display_startup_errors", "On");


if (!defined("CORESHOP_TESTS_PATH")) {
    define('CORESHOP_TESTS_PATH', realpath(dirname(__FILE__)));
}

// some general pimcore definition overwrites
define("PIMCORE_ADMIN", true);
define("PIMCORE_DEBUG", true);
define("PIMCORE_DEVMODE", true);
define("PIMCORE_WEBSITE_VAR",  CORESHOP_TESTS_PATH . "/tmp/var");

@mkdir(CORESHOP_TESTS_PATH . "/output", 0777, true);

// include pimcore bootstrap
include_once(realpath(dirname(__FILE__)) . "/../../../pimcore/cli/startup.php");

// empty temporary var directory
recursiveDelete(PIMCORE_WEBSITE_VAR);
mkdir(PIMCORE_WEBSITE_VAR, 0777, true);

// get default configuration for the test
$testConfig = new \Zend_Config_Xml(CORESHOP_TESTS_PATH . "/config/testconfig.xml");
\Zend_Registry::set("pimcore_config_test", $testConfig);
$testConfig = $testConfig->toArray();

// get configuration from main project
$systemConfigFile = realpath(__DIR__ . "/../../../website/var/config/system.php");
$systemConfig = null;

if (is_file($systemConfigFile)) {
    $systemConfig = new \Zend_Config(include $systemConfigFile);
    $systemConfig = $systemConfig->toArray();

    // this is to allow localhost tests
    $testConfig["rest"]["host"] = "pimcore-local-unittest";
}

$includePathBak = get_include_path();
$includePaths = array(get_include_path());
$includePaths[] = CORESHOP_TESTS_PATH . "/CoreShop/Tests";
array_unshift($includePaths, "/lib/CoreShop");
set_include_path(implode(PATH_SEPARATOR, $includePaths));

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

$setup = new \Pimcore\Model\Tool\Setup();
$setup->config(array(
    "database" => $dbConfig,
    "webservice" => array("enabled" => 1),
    "general" => array("validLanguages" => "en,de")
));

\Pimcore::initConfiguration();

// force the db wrapper to use only one connection, regardless if read/write
if (is_array($systemConfig)) {
    $db = \Pimcore\Db::get();
    $db->setWriteResource($db->getResource());
}

$setup->database();


$setup->contents(array(
    "username" => "admin",
    "password" => microtime()
));

echo "\nSetup done...\n";

// to be sure => reset the database
\Pimcore\Db::reset();
\Pimcore\Cache::disable();

// add the tests, which still reside in the original development unit, not in pimcore_phpunit to the include path
$includePaths = array(
    get_include_path()
);

$includePaths[] = CORESHOP_TESTS_PATH;
$includePaths[] = CORESHOP_TESTS_PATH . "/CoreShop";
$includePaths[] = CORESHOP_TESTS_PATH . "/lib";
$includePaths[] = PIMCORE_PLUGINS_PATH . "/CoreShop/lib";

set_include_path(implode(PATH_SEPARATOR, $includePaths));

// register the tests namespace
$autoloader = \Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('CoreShop');


\Zend_Registry::set("pimcore_admin_user", \Pimcore\Model\User::getByName("admin"));

//install CoreShop
$install = new \CoreShop\Plugin\Install();

$install->createConfig();
\Pimcore::getEventManager()->trigger('coreshop.install.pre', null, array("installer" => $install));

$install->executeSQL("CoreShop");
$install->executeSQL("CoreShop-States");

//install Data
//$install->installObjectData("orderStates");
//$install->installDocuments("documents");

$fcUserAddress = $install->createFieldcollection('CoreShopUserAddress');

// create object classes
$categoryClass = $install->createClass('CoreShopCategory');
$productClass = $install->createClass('CoreShopProduct');
$cartClass = $install->createClass('CoreShopCart');
$cartItemClass = $install->createClass('CoreShopCartItem');
$userClass = $install->createClass("CoreShopUser");
$orderItemClass = $install->createClass("CoreShopOrderItem");
$paymentClass = $install->createClass("CoreShopPayment");
$orderClass = $install->createClass("CoreShopOrder");

/*$coreShopFolder = $install->createFolders();
$install->createCustomView($coreShopFolder, array(
    $productClass->getId(),
    $categoryClass->getId(),
    $cartClass->getId(),
    $cartItemClass->getId(),
    $userClass->getId(),
    $orderItemClass->getId(),
    $orderClass->getId(),
    $paymentClass->getId()
));*/
//$install->createStaticRoutes();
//$install->installAdminTranslations(PIMCORE_PLUGINS_PATH . "/CoreShop/install/translations/admin.csv");
//$install->createImageThumbnails();

\Pimcore::getEventManager()->trigger('coreshop.install.post', null, array("installer" => $install));

$install->setConfigInstalled();

\Pimcore\ExtensionManager::enable("plugin", "CoreShop");
\Pimcore\API\Plugin\Broker::getInstance()->registerPlugin(new \CoreShop\Plugin());

Zend_Session::$_unitTestEnabled = true;
\Zend_Registry::set("Zend_Locale", new \Zend_Locale("en"));

\CoreShop\Test\Data::createData();

/**
 * bootstrap is done, phpunit_pimcore is up and running.
 * It has a database, admin user and a complete config.
 * We can start running our tests against the phpunit_pimcore instance
 */
