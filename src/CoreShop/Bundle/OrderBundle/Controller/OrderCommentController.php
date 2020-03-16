<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\Element\Note;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderCommentController extends PimcoreController
{
    public function listAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        NoteServiceInterface $objectNoteService,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $orderId = $request->get('id');
        $order = $orderRepository->find($orderId);

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

        return $viewHandler->handle(['success' => true, 'comments' => $parsedData]);
    }

    public function addAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        NoteServiceInterface $objectNoteService,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $comment = $request->get('comment');
        $submitAsEmail = $request->get('submitAsEmail') === 'true';
        $orderId = $request->get('id');

        $order = $orderRepository->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $viewHandler->handle(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        try {
            $commentEntity = $objectNoteService->createPimcoreNoteInstance($order, Notes::NOTE_ORDER_COMMENT);
            $commentEntity->setTitle('Order Comment');
            $commentEntity->setDescription(nl2br($comment));
            $commentEntity->addData('submitAsEmail', 'bool', $submitAsEmail);
            $comment = $objectNoteService->storeNote($commentEntity, ['order' => $order, 'submitAsEmail' => $submitAsEmail]);

            return $viewHandler->handle(['success' => true, 'commentId' => $comment->getId()]);
        } catch (\Exception $ex) {
            return $viewHandler->handle(['success' => false, 'message' => $ex->getMessage()]);
        }
    }

    public function deleteAction(
        Request $request,
        NoteServiceInterface $objectNoteService,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $commentId = $request->get('id');

        $commentEntity = $objectNoteService->getNoteById($commentId);

        if (null !== $commentEntity) {
            $commentEntity->delete();
        }

        return $viewHandler->handle(['success' => true]);
    }
}
