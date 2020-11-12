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

namespace CoreShop\Component\Order\Transformer;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TransformerEventDispatcher implements TransformerEventDispatcherInterface
{
    private $eventDispatcher;

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
            $event,
            sprintf('%s.%s.pre_%s', 'coreshop', $modelName, 'transform')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPostEvent($modelName, $model, $params = [])
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.post_%s', 'coreshop', $modelName, 'transform')
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
