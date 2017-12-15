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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Application;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Bundle\OrderBundle\Workflow\WorkflowHelper;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\Model\Workflow;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderWorkflowFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $object = $this->getWorkflowObject();
        $object->save();
    }

    /**
     * @return \Pimcore\Model\Workflow
     */
    private function getWorkflowObject()
    {
        $pimClass = $this->container->getParameter('coreshop.model.order.class');

        $workflowObject = new Workflow();
        $workflowObject->setName('OrderState');
        $workflowObject->setWorkflowSubject([
            'types' => ['object'],
            'classes' => [$pimClass::classId()],
        ]);
        $workflowObject->setDefaultState(WorkflowManagerInterface::ORDER_STATE_INITIALIZED);
        $workflowObject->setDefaultStatus(WorkflowManagerInterface::ORDER_STATUS_INITIALIZED);
        $workflowObject->setAllowUnpublished(true);
        $workflowObject->setStates([
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_INITIALIZED,
                'label' => 'Initialized',
                'color' => '#4d4a4c',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_NEW,
                'label' => 'New',
                'color' => '#9bc4c4',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_PENDING_PAYMENT,
                'label' => 'Pending Payment',
                'color' => '#d0c31f',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_PROCESSING,
                'label' => 'Processing',
                'color' => '#3081ba',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_COMPLETE,
                'label' => 'Complete',
                'color' => '#73a623',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_CLOSED,
                'label' => 'Closed',
                'color' => '#ffc301',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_CANCELED,
                'label' => 'Canceled',
                'color' => '#c12f30',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_ON_HOLD,
                'label' => 'On Hold',
                'color' => '#b9c1bd',
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATE_PAYMENT_REVIEW,
                'label' => 'Payment Review',
                'color' => '#ae61db',
            ],
        ]);
        $workflowObject->setStatuses([
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_INITIALIZED,
                'label' => 'Initialized',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_PENDING,
                'label' => 'Pending',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT,
                'label' => 'Pending Payment',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_PROCESSING,
                'label' => 'Processing',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_COMPLETE,
                'label' => 'Complete',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_CLOSED,
                'label' => 'Closed',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_CANCELED,
                'label' => 'Canceled',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_ON_HOLD,
                'label' => 'On Hold',
                'elementPublished' => true,
            ],
            [
                'name' => WorkflowManagerInterface::ORDER_STATUS_PAYMENT_REVIEW,
                'label' => 'Payment Review',
                'elementPublished' => true,
            ],
        ]);
        $workflowObject->setActions([
            [
                'name' => 'change_order_state',
                'label' => 'Change Order State',
                'transitionTo' => [
                    WorkflowManagerInterface::ORDER_STATE_INITIALIZED => [
                        WorkflowManagerInterface::ORDER_STATUS_INITIALIZED,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_NEW => [
                        WorkflowManagerInterface::ORDER_STATUS_PENDING,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_PENDING_PAYMENT => [
                        WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_PROCESSING => [
                        WorkflowManagerInterface::ORDER_STATUS_PROCESSING,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_COMPLETE => [
                        WorkflowManagerInterface::ORDER_STATUS_COMPLETE,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_CLOSED => [
                        WorkflowManagerInterface::ORDER_STATUS_CLOSED,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_CANCELED => [
                        WorkflowManagerInterface::ORDER_STATUS_CANCELED,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_ON_HOLD => [
                        WorkflowManagerInterface::ORDER_STATUS_ON_HOLD,
                    ],
                    WorkflowManagerInterface::ORDER_STATE_PAYMENT_REVIEW => [
                        WorkflowManagerInterface::ORDER_STATUS_PAYMENT_REVIEW,
                    ],
                ],
                'events' => [
                    'before' => [WorkflowHelper::class, 'beforeDispatchOrderChange'],
                    'success' => [WorkflowHelper::class, 'successDispatchOrderChange'],
                    'failure' => [WorkflowHelper::class, 'failureDispatchOrderChange'],
                ],
                'notes' => [
                    'type' => 'Order State Change',
                    'required' => false,
                ],
            ],
        ]);
        $workflowObject->setTransitionDefinitions([
            WorkflowManagerInterface::ORDER_STATUS_INITIALIZED => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_PENDING => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_PROCESSING => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_COMPLETE => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_CLOSED => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_CANCELED => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_ON_HOLD => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
            WorkflowManagerInterface::ORDER_STATUS_PAYMENT_REVIEW => [
                'validActions' => [
                    'change_order_state' => null,
                ],
            ],
        ]);

        return $workflowObject;
    }
}
