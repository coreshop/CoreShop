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

namespace CoreShop\Bundle\CoreBundle\StateMachine\EventListener;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class WorkflowListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $callbackConfig;

    /**
     * @var ServiceRegistryInterface
     */
    private $serviceRegistry;

    /**
     * @param array                    $callbackConfig
     * @param ServiceRegistryInterface $serviceRegistry
     */
    public function __construct($callbackConfig = [], ServiceRegistryInterface $serviceRegistry)
    {
        $this->callbackConfig = $callbackConfig;
        $this->serviceRegistry = $serviceRegistry;
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.transition' => ['checkCoreShopTransition']
        ];
    }

    /**
     * @param Event $event
     */
    public function checkCoreShopTransition(Event $event)
    {
        if (!isset($this->callbackConfig[$event->getWorkflowName()])) {
            return;
        }

        $workflowCallbacks = $this->callbackConfig[$event->getWorkflowName()];
        $transitionName = $event->getTransition()->getName();

        if (empty($workflowCallbacks['after'])) {
            return;
        }

        $afterActions = $workflowCallbacks['after'];
        foreach ($afterActions as $callback) {
            if (!in_array($transitionName, $callback['on'])) {
                continue;
            }

            if (empty($callback['do']) || count($callback['do']) !== 2) {
                continue;
            }

            $service = $callback['do'][0];
            $method = $callback['do'][1];

            if (!$this->serviceRegistry->has($service)) {
                continue;
            }

            $service = $this->serviceRegistry->get($service);
            $service->$method($event->getSubject());

        }
    }
}