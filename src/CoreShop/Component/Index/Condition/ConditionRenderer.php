<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Index\Condition;

use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

final class ConditionRenderer implements ConditionRendererInterface
{
    public function __construct(
        private ServiceRegistryInterface $registry,
    ) {
    }

    public function render(WorkerInterface $worker, ConditionInterface $condition, array $params = []): mixed
    {
        /**
         * @var DynamicRendererInterface $renderer
         */
        foreach ($this->registry->all() as $renderer) {
            if ($renderer->supports($worker, $condition)) {
                return $renderer->render($worker, $condition, $params);
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No Renderer found for condition with type %s', $condition::class),
        );
    }
}
