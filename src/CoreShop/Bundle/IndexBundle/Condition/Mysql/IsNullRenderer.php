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

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\IsNotNullCondition;
use CoreShop\Component\Index\Condition\IsNullCondition;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class IsNullRenderer extends AbstractMysqlDynamicRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(WorkerInterface $worker, ConditionInterface $condition, string $prefix = null)
    {
        /**
         * @var $condition IsNullCondition
         */
        Assert::isInstanceOf($condition, IsNullCondition::class);

        $operator = 'IS NULL';

        if ($condition instanceof IsNotNullCondition) {
            $operator = 'IS NOT NULL';
        }

        return sprintf('%s %s', $this->quoteFieldName($condition->getFieldName(), $prefix), $operator);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof MysqlWorker && $condition instanceof IsNullCondition;
    }
}
