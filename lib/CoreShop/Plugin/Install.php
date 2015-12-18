<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Plugin;

use CoreShop\Plugin;
use CoreShop\Config;

use Pimcore\Model\Document;
use Pimcore\Model\Object;
use Pimcore\Model\Object\Folder;
use Pimcore\Model\User;
use Pimcore\Model\Staticroute;

use Pimcore\Model\Tool\Setup;

class Install
{
    /**
     * @var User
     */
    protected $_user;

    public function executeSQL($fileName) {
        $file = PIMCORE_PLUGINS_PATH . "/CoreShop/install/sql/$fileName.sql";;

        $setup = new Setup();
        $setup->insertDump($file);
    }

    public function createClass($className)
    {
        $class = Object\ClassDefinition::getByName($className);

        if (!$class)
        {
            $result = Plugin::getEventManager()->trigger("install.class.getClass.$className", $this, array("className" => $className, "json" => $json), function($v) {
                return ($v instanceof Object\ClassDefinition);
            });

            if ($result->stopped()) {
                return $result->last();
            }

            $jsonFile = PIMCORE_PLUGINS_PATH . "/CoreShop/install/class-$className.json";

            $class = Object\ClassDefinition::create();
            $class->setName($className);
            $class->setUserOwner($this->_getUser()->getId());

            $json = file_get_contents($jsonFile);

            $result = Plugin::getEventManager()->trigger('install.class.preCreate', $this, array("className" => $className, "json" => $json), function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });

            if ($result->stopped()) {
                $resultJson = $result->last();

                if($resultJson)
                {
                    $json = $resultJson;
                }
            }

            Object\ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

            return $class;
        }

        return $class;
    }
    
    public function removeClass($name)
    {
        $class = Object\ClassDefinition::getByName($name);
        if ($class) {
            $class->delete();
        }
    }
    
    public function createObjectBrick($name, $jsonPath = null)
    {
        try {
            $objectBrick = Object\Objectbrick\Definition::getByKey($name);
        } 
        catch (\Exception $e) {
            if($jsonPath == null)
                $jsonPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/fieldcollection-$name.json";
            
            $objectBrick = new Object\Objectbrick\Definition();
            $objectBrick->setKey($name);
            
            $json = file_get_contents($jsonPath);
            
            $result = Plugin::getEventManager()->trigger('install.objectbrick.preCreate', $this, array("objectbrickName" => $name, "json" => $json), function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if($resultJson)
                {
                    $json = $resultJson;
                }
            }
            
            Object\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, true);
        }
        
        return $objectBrick;
    }
    
    public function removeObjectBrick($name)
    {
        try
        {
            $brick = Object\Objectbrick\Definition::getByKey($name);

            if ($brick) {
                $brick->delete();
            }
        } 
        catch(\Exception $e)
        {
            return false;
        }
        
        return true;
    }
    
    public function createFieldCollection($name, $jsonPath = null)
    {
        try {
            $fieldCollection = Object\Fieldcollection\Definition::getByKey($name);
        } 
        catch (\Exception $e) {
            if($jsonPath == null)
                $jsonPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/fieldcollection-$name.json";
                
            $fieldCollection = new Object\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
            
            $json = file_get_contents($jsonPath);

            $result = Plugin::getEventManager()->trigger('install.fieldcollection.preCreate', $this, array("fieldcollectionName" => $name, "json" => $json), function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if($resultJson)
                {
                    $json = $resultJson;
                }
            }
            
            Object\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);
        }
        
        return $fieldCollection;
    }
    
    public function removeFieldcollection($name)
    {
        try
        {
            $fc = Object\Fieldcollection\Definition::getByKey($name);

            if ($fc) {
                $fc->delete();
            }
        } 
        catch(\Exception $e)
        {
            return false;
        }
        
        return true;
    }

    public function createFolders()
    {
        $root = Folder::getByPath("/coreshop");
        $products = Folder::getByPath("/coreshop/products");
        $categories = Folder::getByPath("/coreshop/categories");
        $cart = Folder::getByPath("/coreshop/carts");

        if(!$root instanceof Folder)
        {
            $root = Folder::create(array(
                'o_parentId' => 1,
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'coreshop',
                'o_published' => true,
            ));
        }
        
        if(!$products instanceof Folder)
        {
            Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'products',
                'o_published' => true,
            ));
        }
        
        if(!$categories instanceof Folder)
        {
            Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'categories',
                'o_published' => true,
            ));
        }
        
        if(!$cart instanceof Folder)
        {
            Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'carts',
                'o_published' => true,
            ));
        }

        return $root;
    }
    
    public function removeFolders()
    {
        $blogFolder = Folder::getByPath('/coreshop');
        if ($blogFolder) {
            $blogFolder->delete();
        }
    }
    
    public function createCustomView($rootFolder, array $classIds)
    {
        $customViews = \Pimcore\Tool::getCustomViewConfig();
        
        if (!$customViews) {
            $customViews = array();
            $customViewId = 1;
        } else {
            $last = end($customViews);
            $customViewId = $last['id'] + 1;
        }
        $customViews[] = array(
            'name' => 'CoreShop',
            'condition' => '',
            'icon' => '/pimcore/static/img/icon/cart.png',
            'id' => $customViewId,
            'rootfolder' => $rootFolder->getFullPath(),
            'showroot' => false,
            'classes' => implode(',', $classIds),
        );
        $writer = new \Zend_Config_Writer_Xml(array(
            'config' => new \Zend_Config(array('views'=> array('view' => $customViews))),
            'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/customviews.xml'
        ));
        $writer->write();
    }
    public function removeCustomView()
    {
        $customViews = \Pimcore\Tool::getCustomViewConfig();
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

    public function setConfigInstalled() {
        $oldConfig = Config::getConfig();
        $oldValues = $oldConfig->toArray();

        $oldValues['isInstalled'] = true;

        $config = new \Zend_Config($oldValues, true);
        $writer = new \Zend_Config_Writer_Xml(array(
            "config" => $config,
            "filename" => CORESHOP_CONFIGURATION
        ));
        $writer->write();
    }

    public function createStaticRoutes()
    {
        $conf = new \Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/CoreShop/install/staticroutes.xml');
        
        foreach ($conf->routes->route as $def) {
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

    public function installTemplate($template = "default")
    {
        $templatePath = CORESHOP_TEMPLATE_BASE_PATH . "/$template";

        //Get Template Xml
        if(!file_exists("$templatePath/template.xml")) {
            throw new \Exception("Template $template not found");
        }

        $config = new \Zend_Config_Xml("$templatePath/template.xml");
        $config = $config->toArray();

        if(array_key_exists("installation", $config))
        {
            //Install CoreShop Documents
            if(array_key_exists("documents", $config["installation"]))
            {
                $validLanguages = explode(",", \Pimcore\Config::getSystemConfig()->general->validLanguages);

                foreach($validLanguages as $language)
                {
                    $languageDocument = Document::getByPath("/" . $language);

                    if(!$languageDocument instanceof Document) {
                        $languageDocument = new Document\Page();
                        $languageDocument->setParent(Document::getById(1));
                        $languageDocument->setKey($language);
                        $languageDocument->save();
                    }

                    foreach($config["installation"]["documents"] as $value)
                    {
                        foreach($value as $doc)
                        {
                            $document = Document::getByPath("/" . $language . "/" . $doc['path'] . "/" . $doc['key']);

                            if(!$document)
                            {
                                $class = "Pimcore\\Model\\Document\\" . ucfirst($doc['type']);

                                if(\Pimcore\Tool::classExists($class))
                                {
                                    $document = new $class();
                                    $document->setParent(Document::getByPath("/" . $language . "/" . $doc['path']));
                                    $document->setKey($doc['key']);

                                    if($document instanceof Document\PageSnippet) {
                                        if(array_key_exists("action", $doc))
                                            $document->setAction($doc['action']);

                                        if(array_key_exists("controller", $doc))
                                            $document->setController($doc['controller']);

                                        if(array_key_exists("module", $doc))
                                            $document->setModule($doc['module']);
                                    }

                                    $document->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

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
    
    public function createClassmap()
    {
        if(!is_file(Plugin::getClassmapFile()))
        {
            copy(PIMCORE_PLUGINS_PATH . '/CoreShop/install/coreshop_classmap.xml', Plugin::getClassmapFile());
        }
    }
    
    public function removeClassmap()
    {
        if(is_file(Plugin::getClassmapFile()))
        {
            unlink(Plugin::getClassmapFile());
        }
    }

    public function createConfig()
    {
        if(!is_file(CORESHOP_CONFIGURATION))
        {
            copy(PIMCORE_PLUGINS_PATH . '/CoreShop/install/coreshop-config.xml', CORESHOP_CONFIGURATION);
        }
    }

    public function removeConfig()
    {
        if(is_file(CORESHOP_CONFIGURATION))
        {
            unlink(CORESHOP_CONFIGURATION);
        }
    }
    
    public function createImageThumbnails()
    {
        recurse_copy(PIMCORE_PLUGINS_PATH . "/CoreShop/install/thumbnails/image", PIMCORE_WEBSITE_PATH . "/var/config/imagepipelines", true);
    }
    
    public function removeImageThumbnails()
    {
        foreach (glob(PIMCORE_WEBSITE_PATH . "/var/config/imagepipelines/coreshop_*.xml") as $filename) 
        {
            unlink($filename);
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
     * @return User
     */
    protected function _getUser()
    {
        if (!$this->_user) {
            $this->_user = \Zend_Registry::get('pimcore_admin_user');
        }
        return $this->_user;
    }
}