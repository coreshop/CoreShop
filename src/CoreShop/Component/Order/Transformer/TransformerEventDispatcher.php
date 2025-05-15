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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TransformerEventDispatcher implements TransformerEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function dispatchPreEvent(string $modelName, ResourceInterface $model, array $params = []): void
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.pre_%s', 'coreshop', $modelName, 'transform'),
        );
    }

    public function dispatchPostEvent(string $modelName, ResourceInterface $model, array $params = []): void
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.post_%s', 'coreshop', $modelName, 'transform'),
        );
    }

    private function getEvent(ResourceInterface $model, array $params)
    {
        return new GenericEvent($model, $params);
    }
}
