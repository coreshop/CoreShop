<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\Element\Note;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\Request;

class OrderCommentController extends PimcoreController
{
    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function listAction(Request $request)
    {
        $orderId = $request->get('id');
        $order = $this->getOrderRepository()->find($orderId);

        $objectNoteService = $this->get('coreshop.object_note_service');
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
                'submitAsEmail' => isset($noteData['submitAsEmail']) && true === $noteData['submitAsEmail']['data'],
            ];
        }

        return $this->viewHandler->handle(['success' => true, 'comments' => $parsedData]);
    }

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function addAction(Request $request)
    {
        $comment = $request->get('comment');
        $submitAsEmail = 'true' === $request->get('submitAsEmail');
        $orderId = $request->get('id');

        $order = $this->getOrderRepository()->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->viewHandler->handle(['success' => false, 'message' => "Order with ID '$orderId' not found"]);
        }

        try {
            $objectNoteService = $this->get('coreshop.object_note_service');
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

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function deleteAction(Request $request)
    {
        $commentId = $request->get('id');
        $objectNoteService = $this->get('coreshop.object_note_service');
        $commentEntity = $objectNoteService->getNoteById($commentId);
        $commentEntity->delete();

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @return PimcoreRepositoryInterface
     */
    private function getOrderRepository()
    {
        return $this->get('coreshop.repository.order');
    }
}
