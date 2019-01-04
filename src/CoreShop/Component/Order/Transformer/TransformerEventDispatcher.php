<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class TransformerEventDispatcher implements TransformerEventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPreEvent($modelName, $model, $params = [])
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.pre_%s', 'coreshop', $modelName, 'transform'),
            $event
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPostEvent($modelName, $model, $params = [])
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            sprintf('%s.%s.post_%s', 'coreshop', $modelName, 'transform'),
            $event
        );
    }

    /**
     * @param mixed $model
     * @param array $params
     *
     * @return GenericEvent
     */
    private function getEvent($model, $params)
    {
        return new GenericEvent($model, $params);
    }
}
