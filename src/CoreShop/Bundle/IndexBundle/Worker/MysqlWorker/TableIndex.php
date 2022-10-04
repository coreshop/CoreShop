<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Worker\MysqlWorker;

/**
 * @psalm-suppress MissingConstructor
 */
final class TableIndex
{
    public const TABLE_INDEX_TYPE_UNIQUE = 'UNIQUE';

    public const TABLE_INDEX_TYPE_INDEX = 'INDEX';

    private array $columns = [];

    private string $type;

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
