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

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Component\Index\Condition\CompareCondition;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class CompareRenderer extends AbstractMysqlDynamicRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(WorkerInterface $worker, ConditionInterface $condition, $prefix = null)
    {
        /**
         * @var $condition CompareCondition
         */
        Assert::isInstanceOf($condition, CompareCondition::class);

        $value = $condition->getValue();
        $operator = $condition->getOperator();

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' ' . $operator . ' ' . $this->quote($value);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(WorkerInterface $worker, ConditionInterface $condition)
    {
        return $worker instanceof MysqlWorker && $condition instanceof CompareCondition;
    }
}
