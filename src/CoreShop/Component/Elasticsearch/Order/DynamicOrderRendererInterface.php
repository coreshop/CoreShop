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

namespace CoreShop\Component\Index\Order;

use CoreShop\Component\Index\Worker\WorkerInterface;

interface DynamicOrderRendererInterface
{
    /**
     * Renders the condition.
     *
     *
     * @return mixed
     */
    public function render(WorkerInterface $worker, OrderInterface $order, string $prefix = null);

    public function supports(WorkerInterface $worker, OrderInterface $order): bool;
}
