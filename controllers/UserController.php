<?php

use CoreShop\Controller\Action;
use CoreShop\User;
use CoreShop\Tool;
use CoreShop\Plugin;
use CoreShop;

use Pimcore\Model\Object\Fieldcollection\Data\CoreShopUserAddress;

class CoreShop_UserController extends Action 
{
    public function preDispatch() {
        parent::preDispatch();

        if($this->getParam("action") != "login" && $this->getParam("action") != "register") {
            if (!$this->session->user instanceof \CoreShop\Plugin\User) {
                $this->_redirect($this->view->url(array("lang" => $this->language), "coreshop_index"));
                exit;
            }
        }
    }
    
    public function indexAction() {
        
    }

    public function profileAction() {

    }

    public function ordersAction() {

    }

    public function addressesAction() {

    }

    public function settingsAction() {
        $this->view->success = false;

        if($this->getRequest()->isPost())
        {
            try {
                $params = $this->getAllParams();

                if ($params['password']) {
                    if ($params['password'] != $params['repassword'])
                        throw new \Exception("Passwords do not match!");
                }

                $this->session->user->setValues($userParams);
                $this->session->user->save();

                $this->view->success = true;

                if(array_key_exists("_redirect", $params))
                    $this->_redirect($params['_redirect']);
            }
            catch(\Exception $ex)
            {
                $this->view->message = $ex->getMessage();
            }
        }
    }
    
    public function logoutAction() {
        $this->session->user = null;

        $this->_redirect("/" . $this->language . "/shop");
    }

    public function loginAction() {
        $redirect = $this->getParam("_redirect", $this->view->url(array("action" => "address"), "coreshop_checkout"));
        $base = $this->getParam("_base");

        if($this->getRequest()->isPost())
        {
            $user = User::getUniqueByEmail($this->getParam("email"));

            if ($user instanceof Plugin\User) {
                try {
                    $isAuthenticated = $user->authenticate($this->getParam("password"));

                    if($isAuthenticated) {
                        $this->session->user = $user;

                        $this->_redirect($redirect);
                    }
                }
                catch (\Exception $ex) {
                    $this->view->message = $this->view->translate($ex->getMessage());
                }
            }
            else
                $this->view->message = $this->view->translate("User not found");
        }

        if($base)
        {
            $this->_redirect($base);
        }
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
        $this->view->redirect = $this->getParam("redirect", $this->view->url(array("lang" => $this->language, "action" => "addresses"), "coreshop_user", true));
        $update = $this->getParam("address");
        $this->view->isNew = false;

        foreach($this->session->user->getAddresses() as $address)
        {
            if($address->getName() === $update)
                $this->view->address = $address;;
        }

        if(!$this->view->address instanceof CoreShopUserAddress) {
            $this->view->address = new CoreShopUserAddress();
            $this->view->isNew = true;
        }

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

            if($update)
            {
                for($i = 0; $i < count($this->session->user->getAddresses()); $i++)
                {
                    if($this->session->user->getAddresses()->get($i)->getName() == $update)
                    {
                        $this->session->user->getAddresses()->remove($i);
                        break;
                    }
                }
            }

            $this->view->address->setValues($addressParams);

            if($this->view->isNew)
                $adresses->add($this->view->address);
            
            $this->session->user->save();
            
            if(array_key_exists("_redirect", $params))
                $this->_redirect($params['_redirect']);
            else
                $this->_redirect("/de/shop");
        }
    }

    public function deleteaddressAction()
    {
        $address = $this->getParam("address");
        $i = -1;

        foreach($this->session->user->getAddresses() as $a)
        {
            $i++;

            if($a->getName() === $address)
                break;
        }

        if($i >= 0)
            $this->session->user->getAddresses()->remove($i);

        $this->_redirect($this->view->url(array("lang" => $this->language, "action" => "addresses"), "coreshop_user", true));
    }
}
