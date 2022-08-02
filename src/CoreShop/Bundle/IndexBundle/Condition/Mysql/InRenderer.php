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

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\InCondition;
use CoreShop\Component\Index\Condition\NotInCondition;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class InRenderer extends AbstractMysqlDynamicRenderer
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, string $prefix = null)
    {
        /**
         * @var InCondition $condition
         */
        Assert::isInstanceOf($condition, InCondition::class);

        $inValues = [];

        foreach ($condition->getValues() as $value) {
            $inValues[] = $this->quote((string)$value);
        }

        if (count($inValues) > 0) {
            $operator = 'IN';

            if ($condition instanceof NotInCondition) {
                $operator = 'NOT IN';
            }

            return sprintf(
                '%s %s (%s)',
                $this->quoteFieldName($condition->getFieldName(), $prefix),
                $operator,
                implode(',', $inValues)
            );
        }

        return '';
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof MysqlWorker && $condition instanceof InCondition;
    }
}
