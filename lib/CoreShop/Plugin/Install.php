<?php

class CoreShop_Plugin_Install
{
    /**
     * @var User
     */
    protected $_user;

    public function createClass($className)
    {
        $class = Object_Class::getByName($className);
        
        if (!$class)
        {
            $jsonFile = PIMCORE_PLUGINS_PATH . "/CoreShop/install/class-$className.json";
            
            $class = Object_Class::create();
            $class->setName($className);
            $class->setUserOwner($this->_getUser()->getId());
            
            $json = file_get_contents($jsonFile);
            
            $result = CoreShop::getEventManager()->trigger('install.class.preCreate', $this, array("className" => $className, "json" => $json), function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if($resultJson)
                {
                    $json = $resultJson;
                }
            }
            
            Object_Class_Service::importClassDefinitionFromJson($class, $json, true);
            
            return $class;
        }
        
        return $class;
    }
    
    public function removeClass($name)
    {
        $class = Object_Class::getByName($name);
        if ($class) {
            $class->delete();
        }
    }
    
    public function createObjectBrick($name, $jsonPath = null)
    {
        try {
            $objectBrick = Object_Objectbrick_Definition::getByKey($name);
        } 
        catch (Exception $e) {
            if($jsonPath == null)
                $jsonPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/fieldcollection-$name.json";
            
            $objectBrick = new Object_Objectbrick_Definition();
            $objectBrick->setKey($name);
            
            $json = file_get_contents($jsonPath);
            
            $result = CoreShop::getEventManager()->trigger('install.objectbrick.preCreate', $this, array("objectbrickName" => $name, "json" => $json), function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if($resultJson)
                {
                    $json = $resultJson;
                }
            }
            
            Object_Class_Service::importObjectBrickFromJson($objectBrick, $json, true);
        }
        
        return $fieldCollection;
    }
    
    public function removeObjectBrick($name)
    {
        try
        {
            $brick = Object_Objectbrick_Definition::getByKey($name);

            if ($brick) {
                $brick->delete();
            }
        } 
        catch(Exception $e)
        {
            return false;
        }
        
        return true;
    }
    
    public function createFieldCollection($name, $jsonPath = null)
    {
        try {
            $fieldCollection = Object_Fieldcollection_Definition::getByKey($name);
        } 
        catch (Exception $e) {
            if($jsonPath == null)
                $jsonPath = PIMCORE_PLUGINS_PATH . "/CoreShop/install/fieldcollection-$name.json";
            
            $fieldCollection = new Object_Fieldcollection_Definition();
            $fieldCollection->setKey($name);
            
            $json = file_get_contents($jsonPath);
            
            $result = CoreShop::getEventManager()->trigger('install.fieldcollection.preCreate', $this, array("fieldcollectionName" => $name, "json" => $json), function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });
    
            if ($result->stopped()) {
                $resultJson = $result->last();
                
                if($resultJson)
                {
                    $json = $resultJson;
                }
            }
            
            Object_Class_Service::importFieldCollectionFromJson($fieldCollection, $json, true);
        }
        
        return $fieldCollection;
    }

    public function createFolders()
    {
        $root = Object_Folder::getByPath("/coreshop");
        $products = Object_Folder::getByPath("/coreshop/products");
        $cart = Object_Folder::getByPath("/coreshop/categories");
        $categories = Object_Folder::getByPath("/coreshop/carts");
        
        if(!$root instanceof Object_Folder)
        {
            $root = Object_Folder::create(array(
                'o_parentId' => 1,
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'coreshop',
                'o_published' => true,
            ));
        }
        
        if(!$products instanceof Object_Folder)
        {
            $products = Object_Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'products',
                'o_published' => true,
            ));
        }
        
        if(!$categories instanceof Object_Folder)
        {
            Object_Folder::create(array(
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUser()->getId(),
                'o_userModification' => $this->_getUser()->getId(),
                'o_key' => 'categories',
                'o_published' => true,
            ));
        }
        
        if(!$cart instanceof Object_Folder)
        {
            Object_Folder::create(array(
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
        $blogFolder = Object_Folder::getByPath('/coreshop');
        if ($blogFolder) {
            $blogFolder->delete();
        }
    }
    
    public function createCustomView($rootFolder, array $classIds)
    {
        $customViews = Pimcore_Tool::getCustomViewConfig();
        
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
        $writer = new Zend_Config_Writer_Xml(array(
            'config' => new Zend_Config(array('views'=> array('view' => $customViews))),
            'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/customviews.xml'
        ));
        $writer->write();
    }
    public function removeCustomView()
    {
        $customViews = Pimcore_Tool::getCustomViewConfig();
        if ($customViews) {
            foreach ($customViews as $key => $view) {
                if ($view['name'] == 'CoreShop') {
                    unset($customViews[$key]);
                    break;
                }
            }
            $writer = new Zend_Config_Writer_Xml(array(
                'config' => new Zend_Config(array('views'=> array('view' => $customViews))),
                'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/customviews.xml'
            ));
            $writer->write();
        }
    }

    public function createStaticRoutes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/CoreShop/install/staticroutes.xml');
        
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
    public function removeStaticRoutes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/CoreShop/install/staticroutes.xml');
        
        foreach ($conf->routes->route as $def) {
            $route = Staticroute::getByName($def->name);
            if ($route) {
                $route->delete();
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
     * @return User
     */
    protected function _getUser()
    {
        if (!$this->_user) {
            $this->_user = Zend_Registry::get('pimcore_admin_user');
        }
        return $this->_user;
    }
}