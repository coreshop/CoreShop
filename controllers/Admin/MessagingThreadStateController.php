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

use CoreShop\Model\Messaging\Thread\State;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_MessagingThreadStateController
 */
class CoreShop_Admin_MessagingThreadStateController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = ['list'];

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_messaging_thread_state');
        }
    }

    public function listAction()
    {
        $list = State::getList();

        $data = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $state) {
                $data[] = $this->getTreeNodeConfig($state);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(State $state)
    {
        $tmp = [
            'id' => $state->getId(),
            'text' => $state->getName(),
            'qtipCfg' => [
                'title' => 'ID: '.$state->getId(),
            ],
            'name' => $state->getName(),
            'color' => $state->getColor(),
            'count' => $state->getThreadsList()->count(),
        ];

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(['success' => false, 'message' => $this->getTranslator()->translate('Name must be set')]);
        } else {
            $state = State::create();
            $state->setFinished(false);
            $state->setName($name);
            $state->save();

            $this->_helper->json(['success' => true, 'data' => $state]);
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $state = State::getById($id);

        if ($state instanceof State) {
            $this->_helper->json(['success' => true, 'data' => $state->getObjectVars()]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $state = State::getById($id);

        if ($data && $state instanceof State) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $state->setValues($data);
            $state->save();

            $this->_helper->json(['success' => true, 'data' => $state->getObjectVars()]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $state = State::getById($id);

        if ($state instanceof State) {
            $state->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }
}
