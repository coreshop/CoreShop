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
use CoreShop\Model\Product\Filter;
use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_FilterController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array("list");

        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_filters");
        }
    }

    public function listAction()
    {
        $list = new Filter\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Filter $filter)
    {
        $tmp = array(
            "id" => $filter->getId(),
            "text" => $filter->getName(),
            "elementType" => "group",
            "qtipCfg" => array(
                "title" => "ID: " . $filter->getId()
            ),
            "name" => $filter->getName()
        );

        $tmp["leaf"] = true;
        $tmp["iconCls"] = "coreshop_icon_filters";
        $tmp["allowChildren"] = false;

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam("name");

        if (strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        } else {
            $filter = new Filter();
            $filter->setName($name);
            $filter->setResultsPerPage(20);
            $filter->setOrder("desc");
            $filter->setOrderKey("name");
            $filter->setPreConditions(array());
            $filter->setFilters(array());
            $filter->save();

            $this->_helper->json(array("success" => true, "data" => $filter));
        }
    }

    public function getAction()
    {
        $id = $this->getParam("id");
        $filter = Filter::getById($id);

        if ($filter instanceof Filter) {
            $this->_helper->json(array("success" => true, "data" => $filter));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $filter = Filter::getById($id);

        if ($data && $filter instanceof Filter) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $filter->setValues($data);
            $filter->save();

            $this->_helper->json(array("success" => true, "data" => $filter));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam("id");
        $filter = Filter::getById($id);

        if ($filter instanceof Filter) {
            $filter->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }

    public function getConfigAction()
    {
        $this->_helper->json(array(
            "success" => true,
            "conditions" => array() //Filter::$availableConditions
        ));
    }
}