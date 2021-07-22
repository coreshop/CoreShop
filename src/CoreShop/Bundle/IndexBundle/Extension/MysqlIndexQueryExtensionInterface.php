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
    public function preConditionQuery(IndexInterface $index);

    /**
     * @param IndexInterface $index
     * @param QueryBuilder   $queryBuilder
     *
     * @return array
     */
    public function addJoins(IndexInterface $index, QueryBuilder $queryBuilder);
}
