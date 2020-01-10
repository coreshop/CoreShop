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

namespace CoreShop\Bundle\IndexBundle\Order\Mysql;

use CoreShop\Component\Index\Order\DynamicOrderRendererInterface;
use Doctrine\DBAL\Connection;

abstract class AbstractMysqlDynamicRenderer implements DynamicOrderRendererInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $identifier
     *
     * @return string
     */
    protected function quoteIdentifier($identifier)
    {
        return $this->connection->quoteIdentifier($identifier);
    }

    /**
     * @param string|null $prefix
     *
     * @return string
     */
    protected function renderPrefix($prefix = null)
    {
        if (null === $prefix) {
            return '';
        }

        return $prefix . '.';
    }

    /**
     * @param string      $fieldName
     * @param string|null $prefix
     *
     * @return string
     */
    protected function quoteFieldName($fieldName, $prefix = null)
    {
        return $this->renderPrefix($prefix) . $this->quoteIdentifier($fieldName);
    }
}
