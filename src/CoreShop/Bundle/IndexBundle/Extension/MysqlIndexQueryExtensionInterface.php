<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Extension;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Extension\IndexExtensionInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use Doctrine\DBAL\Query\QueryBuilder;

interface MysqlIndexQueryExtensionInterface extends IndexExtensionInterface
{
    /**
     * @return ConditionInterface[]
     */
    public function preConditionQuery(IndexInterface $index): array;

    public function addJoins(IndexInterface $index, QueryBuilder $queryBuilder): void;
}
