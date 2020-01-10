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

namespace CoreShop\Component\Index\Order;

use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

final class OrderRenderer implements OrderRendererInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * {@inheritdoc}
     */
    public function render(WorkerInterface $worker, OrderInterface $condition, string $prefix = null)
    {
        /**
         * @var DynamicOrderRendererInterface $renderer
         */
        foreach ($this->registry->all() as $renderer) {
            if ($renderer->supports($worker, $condition)) {
                return $renderer->render($worker, $condition, $prefix);
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No Renderer found for order with type %s', get_class($condition))
        );
    }

    /**
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }
}
