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

/**
 * @deprecated not supported anymore, will be removed in 2.0.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(ConditionInterface $condition, $prefix = null)
    {
        throw new \InvalidArgumentException('AbstractRenderer is not supported anymore and will be removed in 2.0. Please directly implement RendererInterface instead');
    }
}
