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

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class OrderHistoryLogger
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var NoteServiceInterface
     */
    private $noteService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $noteIdentifier;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param NoteService              $noteService
     * @param TranslatorInterface      $translator
     * @param string                   $noteIdentifier
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        NoteServiceInterface $noteService,
        TranslatorInterface $translator,
        $noteIdentifier
    ) {
        $this->orderRepository = $orderRepository;
        $this->noteService = $noteService;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
    }

    /**
     * @param null $orderId
     * @param null $message
     * @param null $description
     * @param bool $translate
     */
    public function log($orderId = null, $message = null, $description = null, $translate = false)
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return;
        }

        $note = $this->noteService->createPimcoreNoteInstance($order, $this->noteIdentifier);

        $message = strip_tags($message);

        if (true === $translate) {
            $message = $this->translator->trans($message, [], 'admin');
        }

        $note->setTitle($this->translator->trans('coreshop_workflow_order_history_logger_prefix', [], 'admin').': '.$message);

        if (!is_null($description)) {
            $note->setDescription($description);
        }

        $this->noteService->storeNote($note);
    }
}
