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

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * Renders the condition.
     *
     * @param ConditionInterface $condition
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function render(ConditionInterface $condition)
    {
        $type = ucfirst($condition->getType());

        $functionName = 'render'.$type;

        if (method_exists($this, $functionName)) {
            return $this->$functionName($condition);
        }

        throw new \Exception(sprintf('No render function for type %s found', $condition->getType()));
    }
}
