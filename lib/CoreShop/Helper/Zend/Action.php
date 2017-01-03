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

namespace CoreShop\Helper\Zend;

use CoreShop\Controller\Action\Payment;
use Pimcore\Controller\Action\Helper\ViewRenderer;

/**
 * Class Action
 * @package CoreShop\Helper\Zend
 */
class Action
{
    /**
     * @var string
     */
    public $defaultModule;

    /**
     * @var \Zend_Controller_Dispatcher_Interface
     */
    public $dispatcher;

    /**
     * @var \Zend_Controller_Request_Abstract
     */
    public $request;

    /**
     * @var \Zend_Controller_Response_Abstract
     */
    public $response;

    /**
     * Constructor
     *
     * Grab local copies of various MVC objects
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $front   = \Zend_Controller_Front::getInstance();
        $modules = $front->getControllerDirectory();
        if (empty($modules)) {
            //require_once '\Zend/View/Exception.php';
            $e = new \Zend_View_Exception('Action helper depends on valid front controller instance');
            $e->setView($this->view);
            throw $e;
        }

        $request  = $front->getRequest();
        $response = $front->getResponse();

        if (empty($request) || empty($response)) {
            //require_once '\Zend/View/Exception.php';
            $e = new \Zend_View_Exception('Action view helper requires both a registered request and response object in the front controller instance');
            $e->setView($this->view);
            throw $e;
        }

        $this->request       = clone $request;
        $this->response      = clone $response;
        $this->dispatcher    = clone $front->getDispatcher();
        $this->defaultModule = $front->getDefaultModule();
    }

    /**
     * Reset object states
     *
     * @return void
     */
    public function resetObjects()
    {
        $params = $this->request->getUserParams();
        foreach (array_keys($params) as $key) {
            $this->request->setParam($key, null);
        }

        $this->response->clearBody();
        $this->response->clearHeaders()
            ->clearRawHeaders();
    }

    /**
     * Retrieve rendered contents of a controller action
     *
     * If the action results in a forward or redirect, returns empty string.
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module Defaults to default module
     * @param  array $params
     * @return string
     */
    public function action($action, $controller, $module = null, array $params = [])
    {
        $this->resetObjects();
        if (null === $module) {
            $module = $this->defaultModule;
        }

        // clone the view object to prevent over-writing of view variables
        $viewRendererObj = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        \Zend_Controller_Action_HelperBroker::addHelper(new ViewRenderer());

        $this->request->setParams($params)
            ->setModuleName($module)
            ->setControllerName($controller)
            ->setActionName($action)
            ->setDispatched(true);

        $this->dispatcher->dispatch($this->request, $this->response);

        // reset the viewRenderer object to it's original state
        \Zend_Controller_Action_HelperBroker::addHelper($viewRendererObj);

        if (!$this->request->isDispatched()
            || $this->response->isRedirect()) {
            // forwards and redirects render nothing
            return '';
        }

        $return = $this->response->getBody();
        $this->resetObjects();
        return $return;
    }


    /**
     * Clone the current View
     *
     * @return \Zend_View_Interface
     */
    public function cloneView()
    {
        $view = clone $this->view;
        $view->clearVars();
        return $view;
    }
}
