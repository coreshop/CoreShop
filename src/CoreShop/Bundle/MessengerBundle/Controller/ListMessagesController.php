<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MessengerBundle\Controller;

use CoreShop\Bundle\MessengerBundle\Messenger\FailedMessageRepositoryInterface;
use CoreShop\Bundle\MessengerBundle\Messenger\FailureReceiversRepositoryInterface;
use CoreShop\Bundle\MessengerBundle\Messenger\MessageRepositoryInterface;
use CoreShop\Bundle\MessengerBundle\Messenger\ReceiversRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

/**
 * @psalm-suppress InternalClass
 */
class ListMessagesController extends \Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController
{
    public function listReceiverMessageCountAction(ReceiversRepositoryInterface $receiverLocator): Response
    {
        $this->checkPermission('coreshop_permission_messenger');

        $receivers = [];
        foreach ($receiverLocator->getReceiversMapping() as $name => $receiver) {
            $receivers[] = [
                'receiver' => $name,
                'count' => $receiver instanceof MessageCountAwareInterface ? $receiver->getMessageCount() : null,
            ];
        }

        return $this->json(['data' => $receivers, 'success' => true]);
    }

    public function listListableReceiversAction(ReceiversRepositoryInterface $receiverLocator): Response
    {
        $this->checkPermission('coreshop_permission_messenger');

        $receivers = [];
        foreach ($receiverLocator->getListableReceiversMapping() as $name => $receiver) {
            $receivers[] = [
                'receiver' => $name,
                'count' => $receiver instanceof MessageCountAwareInterface ? $receiver->getMessageCount() : null,
            ];
        }

        return $this->json(['data' => $receivers, 'success' => true]);
    }

    public function listFailureReceiversAction(FailureReceiversRepositoryInterface $failureReceivers): Response
    {
        $this->checkPermission('coreshop_permission_messenger');

        $receivers = [];
        foreach ($failureReceivers->getReceiversWithFailureReceivers() as $name) {
            $receivers[] = [
                'receiver' => $name,
            ];
        }

        return $this->json(['data' => $receivers, 'success' => true]);
    }

    public function listFailedMessagesAction(
        Request $request,
        FailedMessageRepositoryInterface $failedMessageRepository,
    ): Response {
        $this->checkPermission('coreshop_permission_messenger');

        $receiverName = $request->attributes->get('receiverName');

        if (!is_string($receiverName)) {
            throw new NotFoundHttpException();
        }

        $result = $failedMessageRepository->listFailedMessages($receiverName, 100);

        return $this->json(['data' => $result, 'success' => true]);
    }

    public function listMessagesAction(
        Request $request,
        MessageRepositoryInterface $messageRepository,
    ): Response {
        $this->checkPermission('coreshop_permission_messenger');

        $receiverName = $request->attributes->get('receiverName');

        if (!is_string($receiverName)) {
            throw new NotFoundHttpException();
        }

        $result = $messageRepository->listMessages($receiverName, 100);

        return $this->json(['data' => $result, 'success' => true]);
    }
}
