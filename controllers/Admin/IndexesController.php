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
use CoreShop\Model\Index;
use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_IndexesController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array("list");

        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_indexes");
        }
    }

    public function listAction()
    {
        $list = new Index\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $group) {
                $data[] = $this->getTreeNodeConfig($group);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Index $index)
    {
        $tmp = array(
            "id" => $index->getId(),
            "text" => $index->getName(),
            "elementType" => "group",
            "qtipCfg" => array(
                "title" => "ID: " . $index->getId()
            ),
            "name" => $index->getName()
        );

        $tmp["leaf"] = true;
        $tmp["iconCls"] = "coreshop_icon_indexes";
        $tmp["allowChildren"] = false;

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam("name");

        if (strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        } else {
            $group = new Index();
            $group->setName($name);
            $group->setType("mysql");
            $group->setConfig(array());
            $group->save();

            $this->_helper->json(array("success" => true, "data" => $group));
        }
    }

    public function getAction()
    {
        $id = $this->getParam("id");
        $group = Index::getById($id);

        if ($group instanceof Index) {
            $data = get_object_vars($group);
            $data['classId'] = \Pimcore\Model\Object\CoreShopProduct::classId();

            $this->_helper->json(array("success" => true, "data" => $data));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $index = Index::getById($id);

        if ($data && $index instanceof Index) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $index->setValues($data);
            $index->save();

            \CoreShop\IndexService::getIndexService()->getWorker($index->getName())->createOrUpdateIndexStructures();

            $this->_helper->json(array("success" => true, "data" => $index));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam("id");
        $group = Index::getById($id);

        if ($group instanceof Index) {
            $group->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }

    public function getTypesAction() {
        $types = \CoreShop\IndexService::getTypes();
        $typesObject = array();

        foreach($types as $type) {
            $typesObject[] = array(
                "name" => $type
            );
        }

        $this->_helper->json($typesObject);
    }

}