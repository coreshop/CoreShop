<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\Element\Note;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderCommentController extends PimcoreController
{
    public function listAction(Request $request): JsonResponse
    {
        $orderId = $this->getParameterFromRequest($request, 'id');
        $order = $this->getOrderRepository()->find($orderId);

        $objectNoteService = $this->get(NoteServiceInterface::class);
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
            $objectNoteService = $this->get(NoteServiceInterface::class);
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
        $commentId = $this->getParameterFromRequest($request,'id');
        $objectNoteService = $this->get(NoteServiceInterface::class);
        $commentEntity = $objectNoteService->getNoteById($commentId);

        if ($commentEntity instanceof Note) {
            /** @psalm-suppress InternalMethod */
            $commentEntity->getDao()->delete();
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    private function getOrderRepository(): PimcoreRepositoryInterface
    {
        return $this->get('coreshop.repository.order');
    }
}
