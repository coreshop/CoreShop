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

namespace CoreShop\Bundle\ElasticsearchBundle\Condition\Elasticsearch;

use CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\IsCondition;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Webmozart\Assert\Assert;

class IsRenderer extends AbstractElasticsearchDynamicRenderer
{
    public function render(WorkerInterface $worker, ConditionInterface $condition, string $prefix = null): string
    {
        /**
         * @var IsCondition $condition
         */
        Assert::isInstanceOf($condition, IsCondition::class);

        $value = $condition->getValue();

        //TODO see this

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' IS ' . ($value ? '' : ' NOT ') . 'NULL';
    }

    public function supports(WorkerInterface $worker, ConditionInterface $condition): bool
    {
        return $worker instanceof ElasticsearchWorker && $condition instanceof IsCondition;
    }
}
