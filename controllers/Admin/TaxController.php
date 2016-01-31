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

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Tax;
use Pimcore\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

class CoreShop_Admin_TaxController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array("list");
        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_taxes");
        }
    }

    public function listAction()
    {
        $list = new Tax\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $tax) {
                $data[] = $this->getTreeNodeConfig($tax);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Tax $tax)
    {
        $tmp = array(
            "id" => $tax->getId(),
            "text" => $tax->getName(),
            "elementType" => "tax",
            "qtipCfg" => array(
                "title" => "ID: " . $tax->getId()
            ),
            "name" => $tax->getName(),
            "rate" => $tax->getRate()
        );

        $tmp["leaf"] = true;
        $tmp["iconCls"] = "coreshop_icon_taxes";
        $tmp["allowChildren"] = false;

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam("name");

        if (strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        } else {
            $tax = new Tax();
            $tax->setName($name);
            $tax->setRate(0);
            $tax->setActive(1);
            $tax->save();

            $this->_helper->json(array("success" => true, "data" => $tax));
        }
    }

    public function getAction()
    {
        $id = $this->getParam("id");
        $tax = Tax::getById($id);

        if ($tax instanceof Tax) {
            $this->_helper->json(array("success" => true, "data" => $tax->getObjectVars()));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $tax = Tax::getById($id);

        if ($data && $tax instanceof Tax) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $tax->setValues($data);
            $tax->save();

            $this->_helper->json(array("success" => true, "data" => $tax));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam("id");
        $tax = Tax::getById($id);

        if ($tax instanceof Tax) {
            $tax->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}
