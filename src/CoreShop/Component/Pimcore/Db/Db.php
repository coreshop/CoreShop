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

namespace CoreShop\Component\Pimcore\Db;

use Doctrine\DBAL\Connection;
use Webmozart\Assert\Assert;

final class Db extends \Pimcore\Db
{
    /**
     * @return Connection
     */
    public static function getDoctrineConnection()
    {
        /**
         * @var Connection $connection
         */
        $connection = self::getConnection();

        Assert::isInstanceOf($connection, Connection::class);

        return $connection;
    }

    /**
     * @param string $table
     *
     * @return array
     */
    public static function getColumns($table)
    {
        $db = static::get();

        $data = $db->fetchAll('SHOW COLUMNS FROM ' . $table);
        $columns = [];

        foreach ($data as $d) {
            $columns[] = $d['Field'];
        }

        return $columns;
    }

    /**
     * Check if table exists.
     *
     * @param string $table
     *
     * @return bool
     */
    public static function tableExists($table)
    {
        $db = static::get();

        $result = $db->fetchAll("SHOW TABLES LIKE '$table'");

        return count($result) > 0;
    }
}
