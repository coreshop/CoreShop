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
            
            Object_Class_Service::importClassDefinitionFromJson($class, $json, true);
            
            return $class;
        }
        
        return false;
    }
    
    public function removeClass($name)
    {
        $class = Object_Class::getByName($name);
        if ($class) {
            $class->delete();
        }
    }

    public function createFolders()
    {
        $root = Object_Folder::create(array(
            'o_parentId' => 1,
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'coreshop',
            'o_published' => true,
        ));
        
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'products',
            'o_published' => true,
        ));
        
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'categories',
            'o_published' => true,
        ));
        
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'carts',
            'o_published' => true,
        ));
        
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