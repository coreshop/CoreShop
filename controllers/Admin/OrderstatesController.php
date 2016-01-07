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

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\OrderState;

use Pimcore\Controller\Action\Admin;

use Pimcore\Tool as PimTool;

class CoreShop_Admin_OrderstatesController extends Admin
{
    public function listAction() {
        $list = new OrderState\Listing();

        $data = array();
        if(is_array($list->getData())){
            foreach ($list->getData() as $orderState) {
                $data[] = $this->getTreeNodeConfig($orderState);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig($orderState) {
        $tmp = array(
            "id" => $orderState->getId(),
            "text" => $orderState->getName(),
            "elementType" => "orderstate",
            "qtipCfg" => array(
                "title" => "ID: " . $orderState->getId()
            ),
            "name" => $orderState->getName()
        );

        $tmp["leaf"] = true;
        $tmp["iconCls"] = "coreshop_icon_order_states";
        $tmp["allowChildren"] = false;

        return $tmp;
    }

    public function addAction() {
        $name = $this->getParam("name");

        if(strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        }
        else {
            $orderState = new OrderState();
            $orderState->setName($name);
            $orderState->setAccepted(0);
            $orderState->setShipped(0);
            $orderState->setEmail(0);
            $orderState->setPaid(0);
            $orderState->setInvoice(0);
            $orderState->save();

            $config = $this->getTreeNodeConfig($orderState);
            $config['success'] = true;

            $this->_helper->json($config);
        }
    }

    public function getAction() {
        $id = $this->getParam("id");
        $oderState = OrderState::getById($id);

        if($oderState instanceof OrderState)
            $this->_helper->json($oderState);
        else
            $this->_helper->json(array("success" => false));
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $oderState = OrderState::getById($id);

        if ($data && $oderState instanceof OrderState) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $oderState->setValues($data);
            $oderState->save();

            $this->_helper->json(array("success" => true, "orderState" => $oderState));
        } else
            $this->_helper->json(array("success" => false));
    }

    public function deleteAction() {
        $id = $this->getParam("id");
        $oderState = OrderState::getById($id);

        if($oderState instanceof OrderState) {
            $oderState->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}