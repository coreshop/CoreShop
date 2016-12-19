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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Model\Zone;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_ZoneController
 */
class CoreShop_Admin_ZoneController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = ['list'];
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_zones');
        }
    }

    public function listAction()
    {
        $list = Zone::getList();
        $list->setOrder('ASC');
        $list->load();

        $zones = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $zone) {
                $zones[] = $this->getTreeNodeConfig($zone);
            }
        }
        $this->_helper->json($zones);
    }

    protected function getTreeNodeConfig($zone)
    {
        $tmpZone = [
            'id' => $zone->getId(),
            'text' => $zone->getName(),
            'qtipCfg' => [
                'title' => 'ID: '.$zone->getId(),
            ],
            'name' => $zone->getName(),
            'active' => intval($zone->getActive()),
        ];

        return $tmpZone;
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $zone = Zone::getById($id);

        if ($zone instanceof Zone) {
            $this->_helper->json(['success' => true, 'data' => $zone]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $zone = Zone::getById($id);

        if ($data && $zone instanceof Zone) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $zone->setValues($data);
            $zone->save();

            $this->_helper->json(['success' => true, 'data' => $zone]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(['success' => false, 'message' => $this->getTranslator()->translate('Name must be set')]);
        } else {
            $zone = Zone::create();
            $zone->setName($name);
            $zone->setActive(1);
            $zone->save();

            $this->_helper->json(['success' => true, 'data' => $zone]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $zone = Zone::getById($id);

        if ($zone instanceof Zone) {
            $zone->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }
}
