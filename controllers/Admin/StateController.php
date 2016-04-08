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
use CoreShop\Model\State;
use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_StateController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array("list");
        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_permission_states");
        }
    }

    public function listAction()
    {
        $list = new State\Listing();
        $list->setOrder("ASC");
        $list->setOrderKey("name");
        $list->load();

        $states = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $state) {
                $states[] = $this->getTreeNodeConfig($state);
            }
        }
        $this->_helper->json($states);
    }

    protected function getTreeNodeConfig($state)
    {
        $tmpState = array(
            "id" => $state->getId(),
            "text" => $state->getName(),
            "elementType" => "state",
            "qtipCfg" => array(
                "title" => "ID: " . $state->getId()
            ),
            "name" => $state->getName()
        );

        $tmpState["leaf"] = true;
        $tmpState["iconCls"] = "coreshop_icon_state";
        $tmpState["allowChildren"] = false;

        return $tmpState;
    }

    public function getAction()
    {
        $id = $this->getParam("id");
        $state = State::getById($id);

        if ($state instanceof State) {
            $this->_helper->json(array("success" => true, "data" => $state));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $state = State::getById($id);

        if ($data && $state instanceof State) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $state->setValues($data);
            $state->save();

            $this->_helper->json(array("success" => true, "data" => $state));
        } else {
            $this->_helper->json(array("success" => false));
        }
    }

    public function addAction()
    {
        $name = $this->getParam("name");

        if (strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        } else {
            $state = new State();
            $state->setName($name);
            $state->setActive(1);
            $state->save();

            $this->_helper->json(array("success" => true, "data" => $state));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam("id");
        $state = State::getById($id);

        if ($state instanceof State) {
            $state->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}
