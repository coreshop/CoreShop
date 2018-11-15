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

namespace CoreShop\Component\Index\Condition;

use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

final class ConditionRenderer implements ConditionRendererInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * {@inheritdoc}
     */
    public function render(WorkerInterface $worker, ConditionInterface $condition, $prefix = null)
    {
        /**
         * @var $renderer DynamicRendererInterface
         */
        foreach ($this->registry->all() as $renderer) {
            if ($renderer->supports($worker, $condition)) {
                return $renderer->render($worker, $condition, $prefix);
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No Renderer found for condition with type %s', get_class($condition))
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
