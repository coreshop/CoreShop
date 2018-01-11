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

namespace CoreShop\Bundle\CoreBundle\Order\StateResolver;

use CoreShop\Component\Core\OrderPaymentTransitions;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Workflow\StateMachineManager;
use Pimcore\Model\Element\Note;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Workflow;

final class OrderHistoryResolver implements StateResolverInterface
{
    /**
     * @var StateMachineManager
     */
    protected $stateMachineManager;

    /**
     * @param StateMachineManager $stateMachineManager
     */
    public function __construct(StateMachineManager $stateMachineManager)
    {
        $this->stateMachineManager = $stateMachineManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Event $event)
    {
        $event->getSubject();
        $workflow = $this->stateMachineManager->get($order, OrderPaymentTransitions::IDENTIFIER);
        $targetTransition = $this->getTargetTransition($order);

        if (null !== $targetTransition) {
            $this->applyTransition($workflow, $order, $targetTransition);
        }
    }

    /**
     * @param Workflow $workflow
     * @param          $subject
     * @param string   $transition
     */
    private function applyTransition(Workflow $workflow, $subject, string $transition)
    {
        if ($workflow->can($subject, $transition)) {
            $workflow->apply($subject, $transition);
        }
    }

    private function add($subject, string $state)
    {
        $note = new Note();
        //$note->setElement($object);
        $note->setDate(time());
        $note->setType("");

        $note->setTitle('Order Mail');
        //$note->addData('document', 'text', $emailDocument->getId());

        $note->save();
    }
}
