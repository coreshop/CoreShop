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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessagingThreadController
 *
 * @Route("/messaging-thread")
 */
class MessagingThreadController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_messaging_thread';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::class;

    public function listAction(Request $request)
    {
        $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::getList();
        $list->setLimit($request->get('limit', 30));
        $list->setOffset($request->get('page', 1) - 1);

        if ($request->get('filter', null)) {
            $conditionFilters = [];
            $conditionFilters[] = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Service::getFilterCondition($request->get('filter'), '\\CoreShop\Bundle\CoreShopLegacyBundle\\Model\\Messaging\\Thread');
            if (count($conditionFilters) > 0 && $conditionFilters[0] !== '(())') {
                $list->setCondition(implode(' AND ', $conditionFilters));
            }
        }

        $sortingSettings = \Pimcore\Admin\Helper\QueryParams::extractSortingSettings($request->request->all());

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
            if ($thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread) {
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

        return $this->json(['success' => true, 'data' => $data, 'count' => count($data), 'total' => $list->getTotalCount()]);
    }

    public function getAction(Request $request)
    {
        $id = $request->get('id');
        $thread = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::getById($id);

        if ($thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread) {
            return $this->json(['success' => true, 'data' => ['thread' => $this->getThreadForAjax($thread), 'messages' => $this->getMessagesForAjax($thread)]]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    public function saveAction(Request $request)
    {
        throw new \Exception("Not allowed");
    }

    public function addAction(Request $request)
    {
        throw new \Exception("Not allowed");
    }

    public function deleteAction(Request $request)
    {
        throw new \Exception("Not allowed");
    }

    public function getStatesAction(Request $request)
    {
        $stats = [];

        $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread\State::getList();

        foreach ($list->load() as $state) {
            if ($state instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread\State) {
                $stats[] = [
                    'id' => $state->getId(),
                    'name' => $state->getName(),
                    'color' => $state->getColor(),
                    'count' => $state->getThreadsList()->count(),
                ];
            }
        }

        return $this->json(['success' => true, 'data' => $stats]);
    }

    public function getContactsWithMessageCountAction(Request $request)
    {
        $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Contact::getList();
        $contacts = [];

        foreach ($list->load() as $contact) {
            if ($contact instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Contact) {
                $contacts[] = [
                    'id' => $contact->getId(),
                    'name' => $contact->getName(),
                    'description' => $contact->getDescription(),
                    'count' => count($contact->getThreads()),
                ];
            }
        }

        return $this->json(['success' => true, 'data' => $contacts]);
    }

    public function changeStatusAction(Request $request)
    {
        $id = $request->get('thread');
        $thread = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::getById($id);
        $status = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread\State::getById($request->get('status'));

        if ($thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread && $status instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread\State) {
            if ($thread->getStatusId() !== $status->getId()) {
                $thread->setStatusId($status->getId());
                $thread->save();
            }

            return $this->json(['success' => true, 'data' => ['thread' => $this->getThreadForAjax($thread)]]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    public function sendMessageAction(Request $request)
    {
        $id = $request->get('thread');
        $thread = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread::getById($id);

        if ($thread instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread) {
            $message = $thread->createMessage($request->get('message'));
            $message->setAdminUserId($this->getUser()->getId());
            $message->save();

            $message->sendNotification('customer-reply', $thread->getEmail());

            return $this->json(['success' => true, 'data' => ['thread' => $this->getThreadForAjax($thread), 'newMessage' => $this->getMessageForAjax($message)]]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * prepare thread-messages for ajax request.
     *
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread $thread
     *
     * @return array
     */
    protected function getMessagesForAjax(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread $thread)
    {
        $messages = $thread->getMessages();
        $messagesData = [];

        foreach ($messages as $message) {
            if ($message instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Message) {
                $messagesData[] = $this->getMessageForAjax($message);
            }
        }

        return $messagesData;
    }

    /**
     * Prepare message for ajax request.
     *
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Message $message
     *
     * @return array
     */
    protected function getMessageForAjax(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Message $message)
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
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread $thread
     * @return array
     */
    protected function getThreadForAjax(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Messaging\Thread $thread)
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

        if ($thread->getUser() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\User) {
            $entry['user'] = $thread->getUser()->getFirstname().' '.$thread->getUser()->getLastname();
            $entry['userId'] = $thread->getUserId();
        }

        if ($thread->getProduct() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Product) {
            $entry['reference'] = $thread->getProduct()->getName().' ('.$thread->getProduct()->getId().')';
        } elseif ($thread->getOrder() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order) {
            $entry['reference'] = $thread->getOrder()->getOrderNumber().' ('.$thread->getOrder()->getId().')';
        }

        return $entry;
    }
}
