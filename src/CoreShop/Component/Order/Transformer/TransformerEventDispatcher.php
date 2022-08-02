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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TransformerEventDispatcher implements TransformerEventDispatcherInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function dispatchPreEvent(string $modelName, ResourceInterface $model, array $params = []): void
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.pre_%s', 'coreshop', $modelName, 'transform')
        );
    }

    public function dispatchPostEvent(string $modelName, ResourceInterface $model, array $params = []): void
    {
        $event = $this->getEvent($model, $params);

        $this->eventDispatcher->dispatch(
            $event,
            sprintf('%s.%s.post_%s', 'coreshop', $modelName, 'transform')
        );
    }

    private function getEvent(ResourceInterface $model, array $params)
    {
        return new GenericEvent($model, $params);
    }
}
