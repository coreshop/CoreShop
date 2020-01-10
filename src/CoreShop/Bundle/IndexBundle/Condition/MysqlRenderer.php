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

namespace CoreShop\Bundle\IndexBundle\Condition;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\ConditionRendererInterface;
use CoreShop\Component\Index\Condition\RendererInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;

/**
 * @deprecated MysqlRenderer is deprecated since 2.0.0, please use the CoreShop\Component\Index\Condition\ConditionRendererInterface instead
 */
class MysqlRenderer implements RendererInterface
{
    /**
     * @var WorkerInterface
     */
    protected $mysqlWorker;

    /**
     * @var ConditionRendererInterface
     */
    protected $renderer;

    public function __construct()
    {
        $this->mysqlWorker = \Pimcore::getContainer()->get('coreshop.index.worker.mysql');
        $this->renderer = \Pimcore::getContainer()->get('coreshop.index.condition.renderer');

        @trigger_error(
            'Class CoreShop\Bundle\IndexBundle\Condition\MysqlRenderer is deprecated since 2.0.0, please use the CoreShop\Component\Index\Condition\ConditionRendererInterface instead.',
            E_USER_DEPRECATED
        );
    }

    public function render(ConditionInterface $condition, $prefix = null)
    {
        return $this->renderer->render($this->mysqlWorker, $condition, $prefix);
    }
}
