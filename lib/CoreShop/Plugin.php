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

use CoreShop\Model\Configuration;
use Pimcore\API\Plugin\AbstractPlugin;
use Pimcore\API\Plugin\PluginInterface;
use CoreShop\Model\Plugin\InstallPlugin;
use CoreShop\Plugin\Install;
use Pimcore\Logger;

/**
 * Class Plugin
 * @package CoreShop
 */
class Plugin extends AbstractPlugin implements PluginInterface
{
    /**
     * @var \Zend_Translate
     */
    protected static $_translate;

    /**
     * Plugin constructor.
     *
     * @param null $jsPaths
     * @param null $cssPaths
     */
    public function __construct($jsPaths = null, $cssPaths = null)
    {
        parent::__construct($jsPaths, $cssPaths);
    }

    /**
     * Init Plugin.
     *
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    public function init()
    {
        require_once PIMCORE_PLUGINS_PATH . "/CoreShop/lib/CoreShop.php";

        \CoreShop::bootstrap($this);
    }


    /**
     * Install Plugin.
     *
     * @param InstallPlugin $installPlugin
     */
    public static function installPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->install($install);
    }

    /**
     * Uninstall Plugin.
     *
     * @param InstallPlugin $installPlugin
     */
    public static function uninstallPlugin(InstallPlugin $installPlugin)
    {
        $install = new Install();
        $installPlugin->uninstall($install);
    }

    /**
     * Install Pimcore CoreShop Plugin.
     *
     * @return mixed
     */
    public static function install()
    {
        try {
            $install = new Install();

            $install->executeSQL('CoreShop');
            $install->executeSQL('CoreShop-States');
            $install->createConfig();

            \Pimcore::getEventManager()->trigger('coreshop.install.post', null, ['installer' => $install]);
        } catch (Exception $e) {
            Logger::crit($e);

            return self::getTranslate()->_('coreshop_install_failed');
        }

        return self::getTranslate()->_('coreshop_installed_successfully');
    }

    /**
     * Uninstall CoreShop.
     *
     * @return string $statusMessage
     */
    public static function uninstall()
    {
        try {
            $install = new Install();

            \Pimcore::getEventManager()->trigger('coreshop.uninstall.pre', null, ['installer' => $install]);

            // remove static routes
            $install->removeStaticRoutes();

            // remove custom view
            $install->removeCustomView();
            // remove object folder with all childs

            $install->removeFolders();
            // remove classes

            $install->removeClass('CoreShopProduct');
            $install->removeClass('CoreShopCategory');
            $install->removeClass('CoreShopCart');
            $install->removeClass('CoreShopCartItem');
            $install->removeClass('CoreShopUser');
            $install->removeClass('CoreShopOrder');
            $install->removeClass('CoreShopPayment');
            $install->removeClass('CoreShopOrderItem');

            $install->removeFieldcollection('CoreShopUserAddress');
            $install->removeImageThumbnails();
            $install->removeConfig();

            \Pimcore::getEventManager()->trigger('coreshop.uninstall.post', null, ['installer' => $install]);

            return self::getTranslate()->_('coreshop_uninstalled_successfully');
        } catch (Exception $e) {
            \Logger::crit($e);

            return self::getTranslate()->_('coreshop_uninstall_failed');
        }
    }

    /**
     * Check if CoreShop is installed.
     *
     * @return bool $isInstalled
     */
    public static function isInstalled()
    {
        $config = Configuration::get('SYSTEM.ISINSTALLED');

        if (!is_null($config)) {
            return true;
        }

        return false;
    }

    /**
     * get translation directory.
     *
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH.'/CoreShop/static/texts';
    }

    /**
     * get translation file.
     *
     * @param string $language
     *
     * @return string path to the translation file relative to plugin directory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory()."/$language.csv")) {
            return "/CoreShop/static/texts/$language.csv";
        } else {
            return '/CoreShop/static/texts/en.csv';
        }
    }

    /**
     * get translate.
     *
     * @param $lang
     *
     * @return \Zend_Translate
     */
    public static function getTranslate($lang = null)
    {
        if (self::$_translate instanceof \Zend_Translate) {
            return self::$_translate;
        }
        if (is_null($lang)) {
            try {
                $lang = \Zend_Registry::get('Zend_Locale')->getLanguage();
            } catch (Exception $e) {
                $lang = 'en';
            }
        }

        self::$_translate = new \Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH.self::getTranslationFile($lang),
            $lang,
            ['delimiter' => ',']
        );

        return self::$_translate;
    }
}
