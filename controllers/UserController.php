<?php

use CoreShop\Controller\Action;
use CoreShop\User;
use CoreShop\Tool;
use CoreShop\Plugin;
use CoreShop;

use Object\Fieldcollection\Data\CoreShopUserAddress;

class CoreShop_UserController extends Action 
{
    public function preDispatch() {
        parent::preDispatch();
    }
    
    public function indexAction() {
        
    }
    
    public function logoutAction() {
        $this->session->user = null;

        $this->_redirect("/" . $this->language . "/shop");
    }
    
    public function registerAction() {
        if($this->getRequest()->isPost())
        {
            $params = $this->getAllParams();
            
            $addressParams = array();
            $userParams = array();
            
            foreach($params as $key=>$value)
            {
                if(startsWith($key, "address_"))
                {
                    $addressKey = str_replace("address_", "", $key);
                    
                    $addressParams[$addressKey] = $value;
                }
                else
                {
                    $userParams[$key] = $value;
                }
            }
            
            $folder = "/users/" . strtolower(substr($userParams['lastname'], 0, 1));
            
            $adresses = new Object\Fieldcollection();
            
            $address = new CoreShopUserAddress();
            $address->setValues($addressParams);
            
            $adresses->add($address);
            
            $user = User::create();
            $user->setKey(Pimcore\File::getValidFilename($userParams['email']));
            $user->setPublished(true);
            $user->setParent(Tool::findOrCreateObjectFolder($folder));
            $user->setValues($userParams);
            $user->setAddresses($adresses);
            $user->save();
            
            Plugin::getEventManager()->trigger('user.postAdd', $this, array("request" => $this->getRequest(), "user" => $user));
            
            $this->session->user = $user;
            
            if(array_key_exists("_redirect", $params))
                $this->_redirect($params['_redirect']);
        }
    }
    
    public function addressAction()
    {
        $this->view->redirect = $this->getParam("redirect");
        
        if($this->getRequest()->isPost())
        {
            $params = $this->getAllParams();
            
            $addressParams = array();
            
            foreach($params as $key=>$value)
            {
                if(startsWith($key, "address_"))
                {
                    $addressKey = str_replace("address_", "", $key);
                    
                    $addressParams[$addressKey] = $value;
                }
            }
            
            $adresses = $this->session->user->getAddresses();
            
            if(!$adresses instanceof Object_Fieldcollection)
                $adresses = new Object\Fieldcollection();
            
            $address = new CoreShopUserAddress();
            $address->setValues($addressParams);
            
            $adresses->add($address);
            
            $this->session->user->save();
            
            if(array_key_exists("_redirect", $params))
                $this->_redirect($params['_redirect']);
            else
                $this->_redirect("/de/shop");
        }
    }
}
