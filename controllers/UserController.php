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

use CoreShop\Controller\Action;
use CoreShop\Exception as CoreShopException;
use CoreShop\Model\Country;
use Pimcore\Model\Object;

/**
 * Class CoreShop_UserController
 */
class CoreShop_UserController extends Action
{

    /**
     * Check if user is allowed here globally.
     */
    public function preDispatch()
    {
        parent::preDispatch();

        //Users are not allowed in CatalogMode
        if (\CoreShop\Model\Configuration::isCatalogMode()) {
            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language], 'coreshop_index'));
        }

        $allowedActionsWithoutLogin = ['login', 'register', 'password-reset-request', 'password-reset', 'guest-order-tracking'];

        if (!in_array($this->getRequest()->getActionName(), $allowedActionsWithoutLogin)) {
            if (!\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
                $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language], 'coreshop_index'));
                exit;
            }
        }
    }

    public function indexAction()
    {
    }

    public function profileAction()
    {
    }

    public function ordersAction()
    {
    }

    public function orderDetailAction()
    {
        $order = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($order);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->redirect(\CoreShop::getTools()->url(['act' => 'orders',  'lang' => $this->language], 'coreshop_user', true));
        }

        if (!$order->getCustomer() instanceof \CoreShop\Model\User || !$order->getCustomer()->getId() === \CoreShop::getTools()->getUser()->getId()) {
            $this->redirect(\CoreShop::getTools()->url(['act' => 'orders',  'lang' => $this->language], 'coreshop_user', true));
        }

        $this->view->messageSent = $this->getParam('messageSent', false);
        $this->view->order = $order;
    }

    public function orderDetailMessageAction()
    {
        $order = $this->getParam('id');
        $messageText = $this->getParam('text');
        $product = $this->getParam('product');

        $order = \CoreShop\Model\Order::getById($order);
        $product = \CoreShop\Model\Product::getById($product);

        if (!$product instanceof CoreShop\Model\Product) {
            $product = null;
        }

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->redirect(\CoreShop::getTools()->url(['act' => 'orders',  'lang' => $this->language], 'coreshop_user', true));
        }

        if (!$order->getCustomer() instanceof \CoreShop\Model\User || !$order->getCustomer()->getId() === \CoreShop::getTools()->getUser()->getId()) {
            $this->redirect(\CoreShop::getTools()->url(['act' => 'orders',  'lang' => $this->language], 'coreshop_user', true));
        }

        $salesContact = \CoreShop\Model\Messaging\Contact::getById(\CoreShop\Model\Configuration::get('SYSTEM.MESSAGING.CONTACT.SALES'));
        $thread = \CoreShop\Model\Messaging\Thread::searchThread($order->getCustomer()->getEmail(), $salesContact->getId(), \CoreShop\Model\Shop::getShop()->getId(), $order->getId(), $product);

        if (!$thread instanceof \CoreShop\Model\Messaging\Thread) {
            $thread = CoreShop\Model\Messaging\Thread::create();
            $thread->setLanguage($order->getLang());
            $thread->setStatusId(\CoreShop\Model\Configuration::get('SYSTEM.MESSAGING.THREAD.STATE.NEW'));
            $thread->setEmail($order->getCustomer()->getEmail());
            $thread->setUser($order->getCustomer());
            $thread->setContact($salesContact);
            $thread->setShopId($order->getShop()->getId());
            $thread->setToken(uniqid());
            $thread->setOrder($order);

            if ($product instanceof \CoreShop\Model\Product) {
                $thread->setProduct($product);
            }

            $thread->save();
        }

        $message = $thread->createMessage($messageText);

        $message->sendNotification('contact', $thread->getContact()->getEmail());

        $this->redirect(\CoreShop::getTools()->url(['act' => 'order-detail', 'id' => $order->getId(), 'messageSent' => true], 'coreshop_user', true));
    }

    public function orderReorderAction()
    {
        $order = $this->getParam('id');
        $order = \CoreShop\Model\Order::getById($order);

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->redirect(\CoreShop::getTools()->url(['act' => 'orders',  'lang' => $this->language], 'coreshop_user', true));
        }

        if (!$order->getCustomer() instanceof \CoreShop\Model\User || !$order->getCustomer()->getId() === \CoreShop::getTools()->getUser()->getId()) {
            $this->redirect(\CoreShop::getTools()->url(['act' => 'orders',  'lang' => $this->language], 'coreshop_user', true));
        }

        $this->cart->addOrderToCart($order, true);

        $this->redirect(\CoreShop::getTools()->url(['act' => 'list', 'lang' => $this->language], 'coreshop_cart', true));
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function downloadVirtualProductAction()
    {
        $orderItemId = $this->getParam('id');

        $orderItem = \CoreShop\Model\Order\Item::getById($orderItemId);

        if ($orderItem instanceof \CoreShop\Model\Order\Item) {
            if ($orderItem->getVirtualAsset() instanceof \Pimcore\Model\Asset) {
                $order = $orderItem->getOrder();

                if ($order instanceof \CoreShop\Model\Order && $order->getIsPayed()) {
                    if ($order->getCustomer() instanceof \CoreShop\Model\User && $order->getCustomer()->getId() === \CoreShop::getTools()->getUser()->getId()) {
                        $this->serveFile($orderItem->getVirtualAsset());
                    }
                }
            }
        }

        //404
        $response = $this->getResponse();
        $response->setHttpResponseCode(404);
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    /**
     * @param \Pimcore\Model\Asset $asset
     */
    private function serveFile(\Pimcore\Model\Asset $asset)
    {
        $size = $asset->getFileSize('noformatting');
        $quoted = sprintf('"%s"', addcslashes(basename($asset->getFilename()), '"\\'));

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $quoted);
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);

        set_time_limit(0);
        $file = @fopen(PIMCORE_ASSET_DIRECTORY . $asset->getFullPath(), 'rb');
        while (!feof($file)) {
            print(@fread($file, 1024*8));
            ob_flush();
            flush();
        }
        exit;
    }

    /**
     * Update user settings.
     */
    public function settingsAction()
    {
        $this->view->success = false;

        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getAllParams();

                if ($params['password']) {
                    if ($params['password'] !== $params['repassword']) {
                        throw new Exception($this->view->translate('Passwords do not match!'));
                    }
                }

                CoreShop\Model\User::validate($params);

                \CoreShop::getTools()->getUser()->setValues($params);
                \CoreShop::getTools()->getUser()->save();

                $this->view->success = true;

                if (array_key_exists('_redirect', $params)) {
                    $this->redirect($params['_redirect']);
                }
            } catch (\Exception $ex) {
                $this->view->message = $ex->getMessage();
            }
        }
    }

    /**
     * logout user, also unlink user cart
     */
    public function logoutAction()
    {
        \CoreShop::getTools()->unsetUser();
        $this->session->cartId = null;

        $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language], 'coreshop_index'));
    }

    public function loginAction()
    {
        if (\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'profile'], 'coreshop_user'));
        }

        $redirect = $this->getParam('_redirect', \CoreShop::getTools()->url(['act' => 'address'], 'coreshop_checkout'));
        $base = $this->getParam('_base');

        if ($this->getRequest()->isPost()) {
            $user = \CoreShop\Model\User::getUniqueByEmail($this->getParam('email'));

            if ($user instanceof \CoreShop\Model\User) {
                try {
                    $isAuthenticated = $user->authenticate($this->getParam('password'));

                    if ($isAuthenticated) {
                        \CoreShop::getTools()->setUser($user);

                        //Reset country
                        unset($this->session->countryId);

                        if (count($this->cart->getItems()) <= 0) {
                            $cart = $user->getLatestCart();

                            if ($cart instanceof \CoreShop\Model\Cart) {
                                $this->session->cartId = $cart->getId();
                            }
                        }

                        $this->redirect($redirect);
                    }
                } catch (Exception $ex) {
                    $this->view->message = $this->view->translate($ex->getMessage());
                }
            } else {
                $this->view->message = $this->view->translate('User not found');
            }
        }

        if ($base) {
            $this->redirect($base.'?message='.$this->view->message);
        }
    }

    /**
     * Allow user to register themselves
     */
    public function registerAction()
    {
        if (\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'profile'], 'coreshop_user'));
        }

        if ($this->getRequest()->isPost()) {
            $params = $this->getAllParams();

            $addressParams = [];
            $userParams = [];

            foreach ($params as $key => $value) {
                if (startsWith($key, 'address_')) {
                    $addressKey = str_replace('address_', '', $key);
                    $addressParams[$addressKey] = $value;
                } else {
                    $userParams[$key] = $value;
                }
            }

            //if address firstname or lastname is missing ( in guest checkout for example ), use the user information!
            if (empty($addressParams['firstname'])) {
                $addressParams['firstname'] = $userParams['firstname'];
            }
            if (empty($addressParams['lastname'])) {
                $addressParams['lastname'] = $userParams['lastname'];
            }

            try {
                $isGuest = intval($this->getParam('isGuest', 0)) === 1;

                if ($isGuest && !\CoreShop\Model\Configuration::isGuestCheckoutActivated()) {
                    throw new CoreShopException\UnsupportedException('Guest checkout is disabled');
                }

                //Check User exists
                if (\CoreShop\Model\User::getUserByEmail($userParams['email']) instanceof \CoreShop\Model\User) {
                    throw new Exception($this->view->translate('E-Mail already exists'));
                }

                $folder = '/users/' . strtolower(substr($userParams['lastname'], 0, 1));
                $key = Pimcore\File::getValidFilename($userParams['email']);

                if ($isGuest) {
                    $folder = '/guests/' . strtolower(substr($userParams['lastname'], 0, 1));
                    $key = Pimcore\File::getValidFilename($userParams['email'] . ' ' . time());
                }

                //Check for European VAT Number
                if (\CoreShop\Model\Configuration::get('SYSTEM.BASE.CHECKVAT')) {
                    if ($addressParams['vatNumber']) {
                        if (!\CoreShop::getTools()->validateVatNumber($addressParams['vatNumber'])) {
                            throw new Exception($this->view->translate('Invalid VAT Number'));
                        }
                    }
                }

                \CoreShop\Model\User::validate($userParams); //Throws Exception if failing
                \CoreShop\Model\User\Address::validate($addressParams); //Throws Exception if failing

                $user = \CoreShop\Model\User::create();
                $user->setKey($key);
                $user->setPublished(true);
                $user->setParent(Pimcore\Model\Object\Service::createFolderByPath($folder));
                $user->setValues($userParams);
                $user->save();

                $address = \CoreShop\Model\User\Address::create();
                $address->setValues($addressParams);
                $address->setPublished(true);
                $address->setCountry(Country::getById($addressParams['country']));
                $address->setParent($user->getPathForAddresses());
                $address->setKey($address->getName() ? $address->getName() : $address->getFirstname() . ' ' . $address->getLastname());
                $address->setKey(Object\Service::getUniqueKey($address));
                $address->save();

                $addresses = $user->getAddresses();

                if (!is_array($addresses)) {
                    $addresses = [];
                }

                $addresses[] = $address;

                $user->setAddresses($addresses);
                $user->save();

                if ($isGuest) {
                    //Set billing and shipping address in cart
                    $this->cart->setBillingAddress($address);
                    $this->cart->setShippingAddress($address);
                    $this->cart->save();
                }

                \Pimcore::getEventManager()->trigger('coreshop.user.postAdd', $this, ['request' => $this->getRequest(), 'user' => $user]);

                \CoreShop::getTools()->setUser($user);

                if (!$isGuest) {
                    \CoreShop\Model\Mail\Rule::apply('user', $user, [
                        'type' => 'register',
                        'recipient' => $user->getEmail()
                    ]);
                }

                if (array_key_exists('_redirect', $params)) {
                    $this->redirect($params['_redirect']);
                } else {
                    $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'profile'], 'coreshop_user'));
                }
            } catch (\Exception $ex) {
                if (array_key_exists('_error', $params)) {
                    $this->redirect($params['_error'].'?error='.$ex->getMessage());
                }

                $this->view->error = $ex->getMessage();
            }
        }
    }

    public function passwordResetAction()
    {
        if (\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'profile'], 'coreshop_user'));
        }

        $message = '';
        $success = false;
        $showForm = true;

        $hash = trim($this->getParam('hash'));

        if( $this->getParam('success') === '1' ) {
            $success = true;
        } else {
            if (empty($hash)) {
                $message = $this->view->translate('invalid password reset link');
                $showForm = false;
            } else {
                $user = \CoreShop\Model\User::getByResetHash($hash, ['limit' => 1]);
                if (!$user instanceof \CoreShop\Model\User) {
                    $message = $this->view->translate('Invalid password reset link');
                    $showForm = false;
                } else {
                    if ($this->getRequest()->isPost()) {
                        $newPass = $this->getParam('password');
                        $newPassRe = $this->getParam('passwordRe');
                        if ($newPass !== $newPassRe) {
                            $message = $this->view->translate('Passwords do not match!');
                        } else {
                            $user->setResetHash(null);
                            $user->setPassword($newPass);
                            $user->save();

                            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'password-reset', 'success' => true], 'coreshop_user'));

                        }
                    }
                }
            }

        }

        $this->view->assign([
            'message' => $message,
            'showForm' => $showForm,
            'success' => $success
        ]);

    }

    public function passwordResetRequestAction()
    {
        $message = '';
        $success = false;

        if ($this->getRequest()->isPost()) {
            $emailAddress = $this->getParam('email');
            if (!\Zend_Validate::is($emailAddress, 'EmailAddress')) {
                $message = $this->view->translate('invalid email address');
            } else {
                $user = \CoreShop\Model\User::getUserByEmail($emailAddress);
                if ($user instanceof \CoreShop\Model\User) {
                    $hash = hash('md5', $user->getId() . $user->getEmail() . mt_rand());
                    $user->setResetHash($hash);
                    $user->save();

                    $resetLink = \Pimcore\Tool::getHostUrl() . \CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'password-reset', 'hash' => $hash], 'coreshop_user');

                    \CoreShop\Model\Mail\Rule::apply('user', $user, [
                        'type' => 'password-reset',
                        'recipient' => $user->getEmail(),
                        'resetLink' => $resetLink,
                    ]);

                }

                $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'password-reset-request', 'success' => true], 'coreshop_user'));

            }
        } else if( $this->getParam('success') === '1' ) {
            $success = true;
        }

        $this->view->assign([
            'message' => $message,
            'success' => $success
        ]);

    }

    public function addressesAction()
    {
    }

    public function addressAction()
    {
        $this->view->redirect = $this->getParam('redirect', \CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'addresses'], 'coreshop_user', true));
        $update = intval($this->getParam('address'));
        $this->view->isNew = false;

        foreach (\CoreShop::getTools()->getUser()->getAddresses() as $address) {
            if ($address->getId() === $update) {
                $this->view->address = $address;
                break;
            }
        }

        if (!$this->view->address instanceof \CoreShop\Model\User\Address) {
            $this->view->address = \CoreShop\Model\User\Address::create();
            $this->view->address->setParent(\CoreShop::getTools()->getUser()->getPathForAddresses());

            $this->view->isNew = true;
        }

        if ($this->getRequest()->isPost()) {
            try {
                $params = $this->getAllParams();

                $addressParams = [];

                foreach ($params as $key => $value) {
                    if (startsWith($key, 'address_')) {
                        $addressKey = str_replace('address_', '', $key);

                        $addressParams[$addressKey] = $value;
                    }
                }

                //Check for European VAT Number
                if (\CoreShop\Model\Configuration::get('SYSTEM.BASE.CHECKVAT')) {
                    if ($addressParams['vatNumber']) {
                        if (!\CoreShop::getTools()->validateVatNumber($addressParams['vatNumber'])) {
                            throw new Exception($this->view->translate('Invalid VAT Number'));
                        }
                    }
                }

                \CoreShop\Model\User\Address::validate($addressParams); //Throws Exception if failing

                $addresses = \CoreShop::getTools()->getUser()->getAddresses();

                if (!is_array($addresses)) {
                    $addresses = [];
                }

                $this->view->address->setValues($addressParams);
                $this->view->address->setCountry(Country::getById($addressParams['country']));

                if ($this->view->isNew) {
                    $this->view->address->setKey(\Pimcore\File::getValidFilename($addressParams['name']));
                    $this->view->address->setKey(\Pimcore\Model\Object\Service::getUniqueKey($this->view->address));
                    $this->view->address->setPublished(true);
                    $this->view->address->save();

                    $addresses[] = $this->view->address;
                    \CoreShop::getTools()->getUser()->setAddresses($addresses);
                } else {
                    $this->view->address->save();
                }

                \CoreShop::getTools()->getUser()->save();

                if (array_key_exists('_redirect', $params)) {
                    $this->redirect($params['_redirect']);
                } else {
                    $this->redirect('/de/shop');
                }
            } catch (Exception $ex) {
                $this->view->error = $ex->getMessage();
            }
        }
    }

    public function addressDeleteAction()
    {
        $address = intval($this->getParam('address'));
        $address = \CoreShop\Model\User\Address::getById($address);

        if ($address instanceof \CoreShop\Model\User\Address) {
            $found = false;

            foreach (\CoreShop::getTools()->getUser()->getAddresses() as $adr) {
                if ($address->getId() === $address->getId()) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $address->delete();
            }
        }

        $this->redirect(CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'addresses'], 'coreshop_user', true));
    }

    /**
     * Show Order vor Guests only
     */
    public function guestOrderTrackingAction()
    {
        if (\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language, 'act' => 'profile'], 'coreshop_user'));
        }

        $orderReference = $this->getParam('orderReference');
        $email = $this->getParam('email');

        if ($this->getRequest()->isPost()) {
            $order = \CoreShop\Model\Order::findByOrderNumber($orderReference);
            try {
                if ($order instanceof \CoreShop\Model\Order) {
                    if ($order->getCustomer() instanceof \CoreShop\Model\User) {
                        if ($order->getCustomer()->getIsGuest()) {
                            if ($order->getCustomer()->getEmail() === $email) {
                                $this->view->order = $order;
                            } else {
                                throw new \Exception($this->view->translate('E-Mail Address does not match.'));
                            }
                        } else {
                            throw new \Exception($this->view->translate('You are not allowed to view this order'));
                        }
                    } else {
                        throw new \Exception($this->view->translate('Order invalid'));
                    }
                } else {
                    throw new \Exception($this->view->translate('Order reference/email not found!'));
                }
            } catch (\Exception $ex) {
                $this->view->error = $ex->getMessage();
            }
        }
    }
}
