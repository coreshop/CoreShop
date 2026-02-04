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

namespace CoreShop\Behat\Service\Index;

use CoreShop\Component\Index\Extension\IndexColumnTypeConfigExtension;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;

class IndexColumnExtension implements IndexColumnTypeConfigExtension
{
    public function getColumnConfig(IndexColumnInterface $column): array
    {
        $config = [];

        if ($column->getColumnType() === MysqlWorkerInterface::FIELD_TYPE_DOUBLE) {
            return ['scale' => 20, 'precision' => 20];
        }

        return $config;
    }

    public function supports(IndexInterface $index): bool
    {
        return $index->getName() === 'extension_column_config';
    }
}
