<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Condition\NotLikeCondition;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class LikeRenderer extends AbstractMysqlDynamicRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(WorkerInterface $worker, ConditionInterface $condition, $prefix = null)
    {
        /**
         * @var $condition LikeCondition
         */
        Assert::isInstanceOf($condition, LikeCondition::class);

        $value = $condition->getValue();
        $pattern = $condition->getPattern();
        $operator = 'LIKE';
        $patternValue = '';

        switch ($pattern) {
            case 'left':
                $patternValue = '%' . $value;

                break;
            case 'right':
                $patternValue = $value . '%';

                break;
            case 'both':
                $patternValue = '%' . $value . '%';

                break;
        }

        if ($condition instanceof NotLikeCondition) {
            $operator = 'NOT LIKE';
        }

        return sprintf(
            '%s %s %s',
            $this->quoteFieldName($condition->getFieldName(), $prefix),
            $operator,
            $this->quote($patternValue)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(WorkerInterface $worker, ConditionInterface $condition)
    {
        return $worker instanceof MysqlWorker && $condition instanceof LikeCondition;
    }
}
