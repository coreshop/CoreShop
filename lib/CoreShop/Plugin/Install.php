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

use CoreShop\Model\Configuration;
use CoreShop\Plugin;
use CoreShop\Version;
use Pimcore\File;
use Pimcore\Model\Document;
use Pimcore\Model\Object;
use Pimcore\Model\Object\Folder;
use Pimcore\Model\Translation\Admin;
use Pimcore\Model\User;
use Pimcore\Model\Staticroute;
use Pimcore\Model\Tool\Setup;
use Pimcore\Tool;

class Install
{
    /**
     * Admin User
     *
     * @var User
     */
    protected $_user;

    /**
     * executes some install SQL
     *
     * @param $fileName
     */
    public function executeSQL($fileName)
    {
        $file = PIMCORE_PLUGINS_PATH . "/CoreShop/install/sql/$fileName.sql";;

        $setup = new Setup();
        $setup->insertDump($file);
    }

    /**
     * creates a mew Class if it doesn't exists
     *
     * @param $className
     * @param bool $updateClass should class be updated if it already exists
     * @return mixed|Object\ClassDefinition
     */
    public function createClass($className, $updateClass = false)
    {
        $class = Object\ClassDefinition::getByName($className);

        if (!$class || $updateClass) {
            $jsonFile = PIMCORE_PLUGINS_PATH . "/CoreShop/install/class-$className.json";
            $json = file_get_contents($jsonFile);

            $result = Plugin::getEventManager()->trigger("install.class.getClass.$className", $this, array("className" => $className, "json" => $json), function ($v) {
                return ($v instanceof Object\ClassDefinition);
            });

            if ($result->stopped()) {
                return $result->last();
            }

            if (!$class) {
                $class = Object\ClassDefinition::create();
            }

            $class->setName($className);
            $class->setUserOwner($this->_getUserId());

            $result = Plugin::getEventManager()->trigger('install.class.preCreate', $this, array("className" => $className, "json" => $json), function ($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });

            if ($result->stopped()) {
                $resultJson = $result->last();

                if ($resultJson) {
                    $json = $resultJson;
                }
            }

            Object\ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

            return $class;
        }

        return $class;
    }

    /**
     * Removes a class definition
     *
     * @param $name
     */
    public function removeClass($name)
    {
        $class = Object\ClassDefinition::getByName($name);
        if ($class) {
            $class->delete();
        }
    }

    /**
     * Creates a new ObjectBrick
     *
     * @param $name
     * @param null $jsonPath
     * @return mixed|Object\Objectbrick\Definition
     */
    public function createObjectBrick($name, $jsonPath = null)
    {
        try {
            $objectBrick = Object\Objectbrick\Definition::getByKey($name);
        } catch (\Exception $e) {
            if ($jsonPath == null) {
                $jsonPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/fieldcollection-$name.json";
            }
            
            $objectBrick = new Object\Objectbrick\Definition();
            $objectBrick->setKey($name);
            
            $json = file_get_contents($jsonPath);
            
            $result = Plugin::getEventManager()->trigger('install.objectbrick.preCreate', $this, array("objectbrickName" => $name, "json" => $json), function ($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if ($resultJson) {
                    $json = $resultJson;
                }
            }
            
            Object\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, true);
        }
        
        return $objectBrick;
    }

    /**
     * Removes an ObjectBrick
     *
     * @param $name
     * @return bool
     */
    public function removeObjectBrick($name)
    {
        try {
            $brick = Object\Objectbrick\Definition::getByKey($name);

            if ($brick) {
                $brick->delete();
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return true;
    }

    /**
     * Creates a FieldCollection
     *
     * @param $name
     * @param null $jsonPath
     * @return mixed|null|Object\Fieldcollection\Definition
     */
    public function createFieldCollection($name, $jsonPath = null)
    {
        try {
            $fieldCollection = Object\Fieldcollection\Definition::getByKey($name);
        } catch (\Exception $e) {
            if ($jsonPath == null) {
                $jsonPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/fieldcollection-$name.json";
            }
                
            $fieldCollection = new Object\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
            
            $json = file_get_contents($jsonPath);

            $result = Plugin::getEventManager()->trigger('install.fieldcollection.preCreate', $this, array("fieldcollectionName" => $name, "json" => $json), function ($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if ($resultJson) {
                    $json = $resultJson;
                }
            }
            
            Object\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);
        }
        
        return $fieldCollection;
    }

    /**
     * Removes a FieldCollection
     *
     * @param $name
     * @return bool
     */
    public function removeFieldcollection($name)
    {
        try {
            $fc = Object\Fieldcollection\Definition::getByKey($name);

            if ($fc) {
                $fc->delete();
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return true;
    }

    /**
     * Create needed CoreShop Folders
     *
     * @return Object\AbstractObject|Folder
     */
    public function createFolders()
    {
        $root = Folder::getByPath("/coreshop");
        $products = Folder::getByPath("/coreshop/products");
        $categories = Folder::getByPath("/coreshop/categories");
        $cart = Folder::getByPath("/coreshop/carts");

        if (!$root instanceof Folder) {
            $root = Folder::create(array(
                'o_parentId' => 1,
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'coreshop',
                'o_published' => true,
            ));
        }
        
        if (!$products instanceof Folder) {
            Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'products',
                'o_published' => true,
            ));
        }
        
        if (!$categories instanceof Folder) {
            Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'categories',
                'o_published' => true,
            ));
        }
        
        if (!$cart instanceof Folder) {
            Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'carts',
                'o_published' => true,
            ));
        }

        return $root;
    }

    /**
     * Remove CoreShop Folders
     */
    public function removeFolders()
    {
        $blogFolder = Folder::getByPath('/coreshop');
        if ($blogFolder) {
            $blogFolder->delete();
        }
    }

    /**
     * Creates CustomView for CoreShop if it doesn't exist
     *
     * @param $rootFolder
     * @param array $classIds
     * @return bool
     * @throws \Zend_Config_Exception
     */
    public function createCustomView($rootFolder, array $classIds)
    {
        $storedViews = Tool::getCustomViewConfig();

        if (empty($storedViews)) {
            $customViewId = 1;
            $storedViews = array();
        } else {
            $last = end($storedViews);
            $customViewId = $last['id'] + 1;
        }

        $alreadyDefined = false;

        // does custom view already exists?
        if (!empty($storedViews)) {
            foreach ($storedViews as $view) {
                if ($view['name'] == 'CoreShop') {
                    $alreadyDefined = true;
                    break;
                }
            }
        }

        if ($alreadyDefined === true) {
            return false;
        }

        $view =  array(
            'name' => 'CoreShop',
            'condition' => '',
            'icon' => '/pimcore/static/img/icon/cart.png',
            'id' => $customViewId,
            'rootfolder' => $rootFolder->getFullPath(),
            'showroot' => false,
            'classes' => implode(',', $classIds)
        );


        $storedViews[] = $view;

        $customViews = array('views' => $storedViews);

        $configFile = \Pimcore\Config::locateConfigFile("customviews.php");
        File::put($configFile, to_php_data_file_format($customViews));

        return true;
    }

    /**
     * Install Admin TranslationsFile
     *
     * @param $csv string Path to CSV File
     * @return boolean
     */
    public function installAdminTranslations($csv)
    {
        Admin::importTranslationsFromFile($csv, true, Tool\Admin::getLanguages());

        return true;
    }

    /**
     * installs some data based from an XML File
     *
     * @param $xml
     */
    public function installObjectData($xml)
    {
        $file = PIMCORE_PLUGINS_PATH . "/CoreShop/install/data/objects/$xml.xml";

        if (file_exists($file)) {
            $config = new \Zend_Config_Xml($file);
            $config = $config->toArray();
            $coreShopNamespace = "\\CoreShop\\Model\\";

            foreach ($config['objects'] as $class=>$amounts) {
                $class = $coreShopNamespace . $class;

                foreach ($amounts as $values) {
                    if (Tool::classExists($class)) {
                        $object = new $class();

                        foreach ($values as $key => $value) {
                            //Localized Value
                            $setter = "set" . ucfirst($key);

                            if (is_array($value)) {
                                foreach ($value as $lang => $val) {
                                    $object->$setter($val, $lang);
                                }
                            } else {
                                $object->$setter($value);
                            }
                        }

                        $object->save();
                    }
                }
            }
        }
    }

    /**
     * Creates some Documents with Data based from XML file
     *
     * @param $xml
     * @throws \Exception
     */
    public function installDocuments($xml)
    {
        $dataPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/data/documents";
        $file = $dataPath . "/$xml.xml";

        if (file_exists($file)) {
            $config = new \Zend_Config_Xml($file);
            $config = $config->toArray();

            if (array_key_exists("documents", $config)) {
                $validLanguages = explode(",", \Pimcore\Config::getSystemConfig()->general->validLanguages);
                $languagesDone = array();

                foreach ($validLanguages as $language) {
                    $languageDocument = Document::getByPath("/" . $language);

                    if (!$languageDocument instanceof Document) {
                        $languageDocument = new Document\Page();
                        $languageDocument->setParent(Document::getById(1));
                        $languageDocument->setKey($language);
                        $languageDocument->save();
                    }

                    foreach ($config["documents"] as $value) {
                        foreach ($value as $doc) {
                            $document = Document::getByPath("/" . $language . "/" . $doc['path'] . "/" . $doc['key']);

                            if (!$document) {
                                $class = "Pimcore\\Model\\Document\\" . ucfirst($doc['type']);

                                if (Tool::classExists($class)) {
                                    $document = new $class();
                                    $document->setParent(Document::getByPath("/" . $language . "/" . $doc['path']));
                                    $document->setKey($doc['key']);
                                    $document->setProperty("language", $language, 'text', true);

                                    if ($document instanceof Document\PageSnippet) {
                                        if (array_key_exists("action", $doc)) {
                                            $document->setAction($doc['action']);
                                        }

                                        if (array_key_exists("controller", $doc)) {
                                            $document->setController($doc['controller']);
                                        }

                                        if (array_key_exists("module", $doc)) {
                                            $document->setModule($doc['module']);
                                        }
                                    }

                                    $document->setProperty("language", "text", $language);
                                    $document->save();

                                    if (array_key_exists("content", $doc)) {
                                        foreach ($doc['content'] as $fieldLanguage=>$fields) {
                                            if ($fieldLanguage !== $language) {
                                                continue;
                                            }

                                            foreach ($fields['field'] as $field) {
                                                $key = $field['key'];
                                                $type = $field['type'];
                                                $content = null;

                                                if (array_key_exists("file", $field)) {
                                                    $file = $dataPath . "/" . $field['file'];

                                                    if (file_exists($file)) {
                                                        $content = file_get_contents($file);
                                                    }
                                                }

                                                if (array_key_exists("value", $field)) {
                                                    $content = $field['value'];
                                                }

                                                if ($content) {
                                                    if ($type === "objectProperty") {
                                                        $document->setValue($key, $content);
                                                    } else {
                                                        $document->setRawElement($key, $type, $content);
                                                    }
                                                }
                                            }
                                        }

                                        $document->save();
                                    }
                                }
                            }

                            //Link translations
                            foreach ($languagesDone as $doneLanguage) {
                                $translatedDocument = Document::getByPath("/" . $doneLanguage . "/" . $doc['path'] . "/" . $doc['key']);

                                if ($translatedDocument) {
                                    $service = new \Pimcore\Model\Document\Service();

                                    $service->addTranslation($document, $translatedDocument, $doneLanguage);
                                }
                            }
                        }
                    }

                    $languagesDone[] = $language;
                }
            }
        }
    }

    /**
     * Removes CoreShop CustomView
     *
     * @throws \Zend_Config_Exception
     */
    public function removeCustomView()
    {
        $customViews = Tool::getCustomViewConfig();
        if ($customViews) {
            foreach ($customViews as $key => $view) {
                if ($view['name'] == 'CoreShop') {
                    unset($customViews[$key]);
                    break;
                }
            }
            $writer = new \Zend_Config_Writer_Xml(array(
                'config' => new \Zend_Config(array('views'=> array('view' => $customViews))),
                'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/customviews.xml'
            ));
            $writer->write();
        }
    }

    /**
     * set isInstalled true in CoreShop Config
     *
     * @throws \Zend_Config_Exception
     */
    public function setConfigInstalled()
    {
        Configuration::set("SYSTEM.ISINSTALLED", true);
    }

    /**
     * Creates CoreShop Static Routes
     */
    public function createStaticRoutes()
    {
        $conf = new \Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/CoreShop/install/staticroutes.xml');
        
        foreach ($conf->routes->route as $def) {
            if (!Staticroute::getByName($def->name)) {
                $route = Staticroute::create();
                $route->setName($def->name);
                $route->setPattern($def->pattern);
                $route->setReverse($def->reverse);
                $route->setModule($def->module);
                $route->setController($def->controller);
                $route->setAction($def->action);
                $route->setVariables($def->variables);
                $route->setPriority($def->priority);
                $route->save();
            }
        }
    }

    /**
     * enables the Theme
     *
     * @param string $template
     * @throws \CoreShop\Exception\ThemeNotFoundException
     */
    public function installTheme($template = "default")
    {
        Plugin::enableTheme($template);
    }

    /**
     * Remove CoreShop Static Routes
     */
    public function removeStaticRoutes()
    {
        $conf = new \Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/CoreShop/install/staticroutes.xml');

        foreach ($conf->routes->route as $def) {
            $route = Staticroute::getByName($def->name);
            if ($route) {
                $route->delete();
            }
        }
    }

    /**
     * Create CoreShop Config
     */
    public function createConfig()
    {
        Configuration::set("SYSTEM.BASE.CURRENCY", null);
        Configuration::set("SYSTEM.BASE.CATALOGMODE", false);
        Configuration::set("SYSTEM.BASE.BUILD", Version::getBuildNumber());
        Configuration::set("SYSTEM.BASE.VERSION", (string) Version::getVersion());
        Configuration::set("SYSTEM.PRODUCT.DEFAULTIMAGE", null);
        Configuration::set("SYSTEM.CATEGORY.DEFAULTIMAGE", null);
        Configuration::set("SYSTEM.TEMPLATE.NAME", "default");
        Configuration::set("SYSTEM.INVOICE.CREATE", true);
        Configuration::set("SYSTEM.INVOICE.PREFIX", "RE");
        Configuration::set("SYSTEM.INVOICE.SUFFIX", "");
        Configuration::set("SYSTEM.MAIL.ORDER.NOTIFICATION", "");
        Configuration::set("SYSTEM.MAIL.ORDER.NOTIFICATION", true);
        Configuration::set("SYSTEM.ORDERSTATE.QUEUE", 1);
        Configuration::set("SYSTEM.ORDERSTATE.PAYMENT", 2);
        Configuration::set("SYSTEM.ORDERSTATE.PREPERATION", 3);
        Configuration::set("SYSTEM.ORDERSTATE.SHIPPING", 4);
        Configuration::set("SYSTEM.ORDERSTATE.DELIVERED", 5);
        Configuration::set("SYSTEM.ORDERSTATE.CANCELED", 6);
        Configuration::set("SYSTEM.ORDERSTATE.REFUND", 7);
        Configuration::set("SYSTEM.ORDERSTATE.ERROR", 8);
        Configuration::set("SYSTEM.ORDERSTATE.OUTOFSTOCK", 9);
        Configuration::set("SYSTEM.ORDERSTATE.BANKWIRE", 10);
        Configuration::set("SYSTEM.ORDERSTATE.OUTOFSTOCK_UNPAID", 11);
        Configuration::set("SYSTEM.ORDERSTATE.COD", 12);
        Configuration::set("SYSTEM.ISINSTALLED", false);
        Configuration::set("SYSTEM.INVOICE.WKHTML", "-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5");
    }

    /**
     * Remove CoreShop Config
     */
    public function removeConfig()
    {
        $configFile = \Pimcore\Config::locateConfigFile('coreshop_configurations');

        if (is_file($configFile  . '.php')) {
            rename($configFile  . '.php', $configFile  . '.BACKUP');
        }
    }

    /**
     * Creates CoreShop Image Thumbnails
     */
    public function createImageThumbnails()
    {
        $images = file_get_contents(PIMCORE_PLUGINS_PATH . "/CoreShop/install/thumbnails/images.json");
        $images = \Zend_Json::decode($images);

        foreach ($images as $name => $values) {
            $thumbnail = \Pimcore\Model\Asset\Image\Thumbnail\Config::getByName($name);

            if (!$thumbnail) {
                $thumbnail = new \Pimcore\Model\Asset\Image\Thumbnail\Config();
            }

            $thumbnail->setName($name);
            $thumbnail->setValues($values);
            $thumbnail->save();
        }
    }

    /**
     * Removes CoreShop Image Thumbnails
     */
    public function removeImageThumbnails()
    {
        if (\Pimcore\Version::getRevision() >= 3608) {
            $definitions = new \Pimcore\Model\Asset\Image\Thumbnail\Config\Listing();
            $definitions = $definitions->getThumbnails();

            foreach ($definitions as $definition) {
                if (strpos($definition->getName(), "coreshop") === 0) {
                    $definition->delete();
                }
            }
        } else {
            foreach (glob(PIMCORE_WEBSITE_PATH . "/var/config/imagepipelines/coreshop_*.xml") as $filename) {
                unlink($filename);
            }
        }
    }

/*
    public function createDocTypes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/Blog/install/doctypes.xml');
        foreach ($conf->doctypes->doctype as $def) {
            $docType = Document_DocType::create();
            $docType->setName($def->name);
            $docType->setType($def->type);
            $docType->setModule($def->module);
            $docType->setController($def->controller);
            $docType->setAction($def->action);
            $docType->save();
        }
    }
    public function removeDocTypes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/Blog/install/doctypes.xml');
        $names = array();
        foreach ($conf->doctypes->doctype as $def) {
            $names[] = $def->name;
        }
        $list = new Document_DocType_List();
        $list->load();
        foreach ($list->docTypes as $docType) {
            if (in_array($docType->name, $names)) {
                $docType->delete();
            }
        }
    }
*/
    /**
     * @return \Int User Id
     */
    protected function _getUserId()
    {
        $userId = 0;
        $user = Tool\Admin::getCurrentUser();
        if ($user) {
            $userId = $user->getId();
        }
        return $userId;
    }
}
