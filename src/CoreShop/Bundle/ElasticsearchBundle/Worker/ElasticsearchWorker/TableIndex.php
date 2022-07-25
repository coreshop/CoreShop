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

namespace CoreShop\Bundle\ElasticsearchBundle\Worker\ElasticsearchWorker;

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

    public function setColumns(array $columns)
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

    public function setType(string $type)
    {
        $this->type = $type;
    }
}
