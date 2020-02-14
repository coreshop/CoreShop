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

namespace CoreShop\Bundle\IndexBundle\Condition\Mysql;

use CoreShop\Component\Index\Condition\DynamicRendererInterface;
use Doctrine\DBAL\Connection;

abstract class AbstractMysqlDynamicRenderer implements DynamicRendererInterface
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return $this->connection->quoteIdentifier($identifier);
    }

    protected function quote(string $identifier): string
    {
        return $this->connection->quote($identifier);
    }

    protected function renderPrefix(?string $prefix): string
    {
        if (null === $prefix) {
            return '';
        }

        return $prefix . '.';
    }

    protected function quoteFieldName(string $fieldName, string $prefix = null): string
    {
        return $this->renderPrefix($prefix) . $this->quoteIdentifier($fieldName);
    }
}
