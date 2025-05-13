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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\Element\Note;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class OrderCommentController extends PimcoreController
{
    public function listAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        $objectNoteService = $this->container->get(NoteServiceInterface::class);
        $notes = $objectNoteService->getObjectNotes($order, Notes::NOTE_ORDER_COMMENT);

        $parsedData = [];
        /** @var Note $note */
        foreach ($notes as $note) {
            $user = User::getById($note->getUser());
            $noteData = $note->getData();
            $parsedData[] = [
                'id' => $note->getId(),
                'text' => $note->getDescription(),
                'date' => $note->getDate(),
                'userName' => $user ? $user->getName() : 'anonymous',
                'submitAsEmail' => isset($noteData['submitAsEmail']) && $noteData['submitAsEmail']['data'] === true,
            ];
        }

        return $this->viewHandler->handle(['success' => true, 'comments' => $parsedData]);
    }

    public function addAction(Request $request): JsonResponse
    {
        $comment = $this->getParameterFromRequest($request, 'comment');
        $submitAsEmail = $this->getParameterFromRequest($request, 'submitAsEmail') === 'true';
        $orderId = $this->getParameterFromRequest($request, 'id');

        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        try {
            $objectNoteService = $this->container->get(NoteServiceInterface::class);
            $commentEntity = $objectNoteService->createPimcoreNoteInstance($order, Notes::NOTE_ORDER_COMMENT);
            $commentEntity->setTitle('Order Comment');
            $commentEntity->setDescription(nl2br($comment));
            $commentEntity->addData('submitAsEmail', 'bool', $submitAsEmail);
            $comment = $objectNoteService->storeNote($commentEntity, ['order' => $order, 'submitAsEmail' => $submitAsEmail]);

            return $this->viewHandler->handle(['success' => true, 'commentId' => $comment->getId()]);
        } catch (\Exception $ex) {
            return $this->viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    public function deleteAction(Request $request): JsonResponse
    {
        $commentId = $this->getParameterFromRequest($request, 'id');

        $objectNoteService = $this->container->get(NoteServiceInterface::class);
        $commentEntity = $objectNoteService->getNoteById((int) $commentId);

        if ($commentEntity instanceof Note) {
            /** @psalm-suppress InternalMethod */
            $commentEntity->getDao()->delete();
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    private function getOrderRepository(): PimcoreRepositoryInterface
    {
        return $this->container->get('coreshop.repository.order');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
                new SubscribedService('coreshop.repository.order', OrderRepositoryInterface::class),
                new SubscribedService('event_dispatcher', EventDispatcherInterface::class),
                new SubscribedService(NoteServiceInterface::class, NoteServiceInterface::class),
            ]);
    }
}
