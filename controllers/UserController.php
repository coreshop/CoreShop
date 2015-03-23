<?php

class CoreShop_UserController extends CoreShop_Controller_Action 
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
            
            $adresses = new Object_Fieldcollection();
            
            $address = new Object_Fieldcollection_Data_CoreShopUserAddress();
            $address->setValues($addressParams);
            
            $adresses->add($address);
            
            $user = CoreShop_User::create();
            $user->setKey(Pimcore_File::getValidFilename($userParams['email']));
            $user->setPublished(true);
            $user->setParent(CoreShop_Tool::findOrCreateObjectFolder($folder));
            $user->setValues($userParams);
            $user->setAddresses($adresses);
            $user->save();
            
            CoreShop::getEventManager()->trigger('user.postAdd', $this, array("request" => $this->getRequest(), "user" => $user));
            
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
                $adresses = new Object_Fieldcollection();
            
            $address = new Object_Fieldcollection_Data_CoreShopUserAddress();
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
