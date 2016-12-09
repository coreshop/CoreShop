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

use CoreShop\Model\Shop;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_ShopController
 */
class CoreShop_Admin_ShopController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');
        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_shops');
        }
    }
    
    public function listSitesAction()
    {
        $list = new \Pimcore\Model\Site\Listing();
        $list->setOrder('ASC');
        $list->load();

        $sites = array();
        if (is_array($list->getSites())) {
            foreach ($list->getSites() as $site) {
                $sites[] = [
                    'id' => $site->getId(),
                    'rootId' => $site->getRootId(),
                    'name' => $site->getMainDomain()
                ];
            }
        }
        $this->_helper->json($sites);
    }

    public function listAction()
    {
        $list = Shop::getList();
        $list->setOrder('ASC');
        $list->load();

        $shops = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $shop) {
                $shops[] = $this->getTreeNodeConfig($shop);
            }
        }
        $this->_helper->json($shops);
    }

    protected function getTreeNodeConfig(Shop $shop)
    {
        $tmpShop = array(
            'id' => $shop->getId(),
            'text' => $shop->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$shop->getId(),
            ),
            'name' => $shop->getName(),
        );

        return $tmpShop;
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $shop = Shop::getById($id);

        if ($shop instanceof Shop) {
            $this->_helper->json(array('success' => true, 'data' => $shop));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $shop = Shop::getById($id);

        if ($data && $shop instanceof Shop) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $shop->setValues($data);
            $shop->save();

            $this->_helper->json(array('success' => true, 'data' => $shop));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $shop = Shop::create();
            $shop->setName($name);
            $shop->setTemplate(Shop::getDefaultShop()->getTemplate());
            $shop->save();

            $this->_helper->json(array('success' => true, 'data' => $shop));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $shop = Shop::getById($id);

        if ($shop instanceof Shop) {
            $shop->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
