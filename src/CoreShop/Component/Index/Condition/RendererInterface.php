<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Condition;

/**
 * @deprecated RendererInterface is deprecated since 2.0.0, please use the CoreShop\Component\Index\Condition\DynamicRendererInterface instead
 */
interface RendererInterface
{
    /**
     * Renders the condition.
     *
     * @param ConditionInterface $condition
     * @param string             $prefix
     *
     * @return mixed
     */
    public function render(ConditionInterface $condition, $prefix = null);
}
