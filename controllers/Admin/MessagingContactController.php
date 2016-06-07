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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
use CoreShop\Model\Messaging\Contact;
use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_MessagingContactController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = array('list');

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_messaging_contact');
        }
    }

    public function listAction()
    {
        $list = new Contact\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $contact) {
                $data[] = $this->getTreeNodeConfig($contact);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig(Contact $contact)
    {
        $tmp = array(
            'id' => $contact->getId(),
            'text' => $contact->getName(),
            'qtipCfg' => array(
                'title' => 'ID: '.$contact->getId(),
            ),
            'name' => $contact->getName(),
        );

        return $tmp;
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(array('success' => false, 'message' => $this->getTranslator()->translate('Name must be set')));
        } else {
            $contact = new Contact();
            $contact->setName($name);
            $contact->save();

            $this->_helper->json(array('success' => true, 'data' => $contact));
        }
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $contact = Contact::getById($id);

        if ($contact instanceof Contact) {
            $this->_helper->json(array('success' => true, 'data' => $contact->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $contact = Contact::getById($id);

        if ($data && $contact instanceof Contact) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $contact->setValues($data);
            $contact->save();

            $this->_helper->json(array('success' => true, 'data' => $contact->getObjectVars()));
        } else {
            $this->_helper->json(array('success' => false));
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $contact = Contact::getById($id);

        if ($contact instanceof Contact) {
            $contact->delete();

            $this->_helper->json(array('success' => true));
        }

        $this->_helper->json(array('success' => false));
    }
}
