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

namespace CoreShop\Bundle\IndexBundle\Extension;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use Doctrine\DBAL\Query\QueryBuilder;

interface MysqlIndexQueryExtensionInterface extends IndexExtensionInterface
{
    /**
     * @param IndexInterface $index
     *
     * @return ConditionInterface[]
     */
    public function preConditionQuery(IndexInterface $index): array;

    /**
     * @param IndexInterface $index
     * @param QueryBuilder   $queryBuilder
     */
    public function addJoins(IndexInterface $index, QueryBuilder $queryBuilder): void;
}
