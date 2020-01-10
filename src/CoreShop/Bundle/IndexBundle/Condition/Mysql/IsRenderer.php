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

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\IsCondition;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class IsRenderer extends AbstractMysqlDynamicRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(WorkerInterface $worker, ConditionInterface $condition, $prefix = null)
    {
        /**
         * @var $condition IsCondition
         */
        Assert::isInstanceOf($condition, IsCondition::class);

        $value = $condition->getValue();

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' IS ' . ($value ? '' : ' NOT ') . 'NULL';
    }

    /**
     * {@inheritdoc}
     */
    public function supports(WorkerInterface $worker, ConditionInterface $condition)
    {
        return $worker instanceof MysqlWorker && $condition instanceof IsCondition;
    }
}
