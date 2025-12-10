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

namespace CoreShop\Component\Index\Extension;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;

class StringIndexColumnTypeConfigExtension implements IndexColumnTypeConfigExtension
{
    public function getColumnConfig(IndexColumnInterface $column): array
    {
        if ($column->getColumnType() === MysqlWorkerInterface::FIELD_TYPE_STRING) {
            return ['length' => 255];
        }

        return [];
    }

    public function supports(IndexInterface $index): bool
    {
        return $index->getWorker() === 'mysql';
    }
}
