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

use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_MessagingThreadController
 */
class CoreShop_Admin_MessagingThreadController extends Admin
{
    public function init()
    {
        parent::init();

        // check permissions
        $notRestrictedActions = [];

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_messaging_thread');
        }
    }

    public function listAction()
    {
        $list = \CoreShop\Model\Messaging\Thread::getList();
        $list->setLimit($this->getParam('limit', 30));
        $list->setOffset($this->getParam('page', 1) - 1);

        if ($this->getParam('filter', null)) {
            $conditionFilters = [];
            $conditionFilters[] = \CoreShop\Model\Service::getFilterCondition($this->getParam('filter'), '\\CoreShop\\Model\\Messaging\\Thread');
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($this->getAllParams());

        $order = 'DESC';
        $orderKey = 'id';

        if ($sortingSettings['order']) {
            $order = $sortingSettings['order'];
        }
        if (strlen($sortingSettings['orderKey']) > 0) {
            $orderKey = $sortingSettings['orderKey'];
        }

        $list->setOrder($order);
        $list->setOrderKey($orderKey);

        $data = [];

        foreach ($list->load() as $thread) {
            if ($thread instanceof CoreShop\Model\Messaging\Thread) {
                $entry = $this->getThreadForAjax($thread);

                $messages = $thread->getMessages();
                $messagesShort = [];

                foreach ($messages as $message) {
                    $messagesShort[] = substr(strip_tags($message->getMessage()), 0, 50);

                    if (!$entry['admin']) {
                        if ($message->getAdminUserId()) {
                            $adminUser = \Pimcore\Model\User::getById($message->getAdminUserId());

                            if ($adminUser instanceof \Pimcore\Model\User) {
                                $entry['admin'] = $adminUser->getName();
                                $entry['adminId'] = $adminUser->getId();
                            }
                        }
                    }
                }

                $entry['messages'] = implode(',', $messagesShort);

                $data[] = $entry;
            }
        }

        $this->_helper->json(['success' => true, 'data' => $data, 'count' => count($data), 'total' => $list->getTotalCount()]);
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $thread = \CoreShop\Model\Messaging\Thread::getById($id);

        if ($thread instanceof \CoreShop\Model\Messaging\Thread) {
            $this->_helper->json(['success' => true, 'data' => ['thread' => $this->getThreadForAjax($thread), 'messages' => $this->getMessagesForAjax($thread)]]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function getStatesAction()
    {
        $stats = [];

        $list = CoreShop\Model\Messaging\Thread\State::getList();

        foreach ($list->load() as $state) {
            if ($state instanceof \CoreShop\Model\Messaging\Thread\State) {
                $stats[] = [
                    'id' => $state->getId(),
                    'name' => $state->getName(),
                    'color' => $state->getColor(),
                    'count' => $state->getThreadsList()->count(),
                ];
            }
        }

        $this->_helper->json(['success' => true, 'data' => $stats]);
    }

    public function getContactsWithMessageCountAction()
    {
        $list = \CoreShop\Model\Messaging\Contact::getList();
        $contacts = [];

        foreach ($list->load() as $contact) {
            if ($contact instanceof \CoreShop\Model\Messaging\Contact) {
                $contacts[] = [
                    'id' => $contact->getId(),
                    'name' => $contact->getName(),
                    'description' => $contact->getDescription(),
                    'count' => count($contact->getThreads()),
                ];
            }
        }

        $this->_helper->json(['success' => true, 'data' => $contacts]);
    }

    public function changeStatusAction()
    {
        $id = $this->getParam('thread');
        $thread = \CoreShop\Model\Messaging\Thread::getById($id);
        $status = \CoreShop\Model\Messaging\Thread\State::getById($this->getParam('status'));

        if ($thread instanceof \CoreShop\Model\Messaging\Thread && $status instanceof \CoreShop\Model\Messaging\Thread\State) {
            if ($thread->getStatusId() !== $status->getId()) {
                $thread->setStatusId($status->getId());
                $thread->save();
            }

            $this->_helper->json(['success' => true, 'data' => ['thread' => $this->getThreadForAjax($thread)]]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function sendMessageAction()
    {
        $id = $this->getParam('thread');
        $thread = \CoreShop\Model\Messaging\Thread::getById($id);

        if ($thread instanceof \CoreShop\Model\Messaging\Thread) {
            $message = $thread->createMessage($this->getParam('message'));
            $message->setAdminUserId($this->getUser()->getId());
            $message->save();

            $message->sendNotification('customer-reply', $thread->getEmail());

            $this->_helper->json(['success' => true, 'data' => ['thread' => $this->getThreadForAjax($thread), 'newMessage' => $this->getMessageForAjax($message)]]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    /**
     * prepare thread-messages for ajax request.
     *
     * @param \CoreShop\Model\Messaging\Thread $thread
     *
     * @return array
     */
    protected function getMessagesForAjax(\CoreShop\Model\Messaging\Thread $thread)
    {
        $messages = $thread->getMessages();
        $messagesData = [];

        foreach ($messages as $message) {
            if ($message instanceof CoreShop\Model\Messaging\Message) {
                $messagesData[] = $this->getMessageForAjax($message);
            }
        }

        return $messagesData;
    }

    /**
     * Prepare message for ajax request.
     *
     * @param \CoreShop\Model\Messaging\Message $message
     *
     * @return array
     */
    protected function getMessageForAjax(\CoreShop\Model\Messaging\Message $message)
    {
        $data = $message->getObjectVars();

        if ($message->getAdminUserId()) {
            $adminUser = \Pimcore\Model\User::getById($message->getAdminUserId());

            if ($adminUser instanceof \Pimcore\Model\User) {
                $data['admin'] = $adminUser->getObjectVars();
            }
        } else {
            $data['user'] = [
                'email' => $message->getThread()->getEmail(),
            ];
        }

        return $data;
    }


    /**
     * Prepare Thread for Ajax Request
     *
     * @param \CoreShop\Model\Messaging\Thread $thread
     * @return array
     */
    protected function getThreadForAjax(CoreShop\Model\Messaging\Thread $thread)
    {
        $entry = [
            'id' => $thread->getId(),
            'email' => $thread->getEmail(),
            'contactId' => $thread->getContactId(),
            'language' => $thread->getLanguage(),
            'statusId' => $thread->getStatusId(),
            'userId' => null,
            'user' => null,
            'adminId' => null,
            'admin' => null,
            'messages' => '',
            'token' => $thread->getToken(),
            'reference' => null,
            'shopId' => $thread->getShopId()
        ];

        if ($thread->getUser() instanceof \CoreShop\Model\User) {
            $entry['user'] = $thread->getUser()->getFirstname().' '.$thread->getUser()->getLastname();
            $entry['userId'] = $thread->getUserId();
        }

        if ($thread->getProduct() instanceof \CoreShop\Model\Product) {
            $entry['reference'] = $thread->getProduct()->getName().' ('.$thread->getProduct()->getId().')';
        } elseif ($thread->getOrder() instanceof \CoreShop\Model\Order) {
            $entry['reference'] = $thread->getOrder()->getOrderNumber().' ('.$thread->getOrder()->getId().')';
        }

        return $entry;
    }
}
