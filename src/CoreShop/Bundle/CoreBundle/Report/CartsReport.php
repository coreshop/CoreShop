<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\CoreBundle\Report;

use Carbon\Carbon;
use CoreShop\Component\Core\Report\ReportInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\ParameterBag;

class CartsReport implements ReportInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var array
     */
    private $pimcoreClasses;

    /**
     * CartsReport constructor.
     * @param Connection $db
     * @param array $pimcoreClasses
     */
    public function __construct(Connection $db, array $pimcoreClasses)
    {
        $this->db = $db;
        $this->pimcoreClasses = $pimcoreClasses;
    }


    /**
     * {@inheritdoc}
     */
    public function getData(ParameterBag $parameterBag) {
        $fromFilter = $parameterBag->get('from' , strtotime(date('01-m-Y')));
        $toFilter = $parameterBag->get('to', strtotime(date('t-m-Y')));
        $from = Carbon::createFromTimestamp($fromFilter);
        $to = Carbon::createFromTimestamp($toFilter);

        $orderClassId = $this->pimcoreClasses['order'];
        $cartClassId = $this->pimcoreClasses['cart'];

        $queries = [];

        $fromTimestamp = $from->getTimestamp();
        $toTimestamp = $to->getTimestamp();

        foreach (['LEFT', 'RIGHT'] as $join) {
            $queries[] = "
                SELECT
                    CASE WHEN orderDateTimestamp IS NULL THEN cartDateTimestamp ELSE orderDateTimestamp END as timestamp,
                    CASE WHEN orderCount IS NULL THEN 0 ELSE orderCount END as orders,
                    CASE WHEN cartCount IS NULL THEN 0 ELSE cartCount END as carts
                FROM (
                  SELECT 
                    COUNT(*) as orderCount,
                    DATE(FROM_UNIXTIME(orderDate)) as orderDateTimestamp
                  FROM object_query_$orderClassId AS orders
                  WHERE orderDate > $fromTimestamp AND orderDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(orderDate))
                ) as ordersQuery
                $join OUTER JOIN (
                  SELECT
                    COUNT(*) as cartCount,
                    DATE(FROM_UNIXTIME(o_creationDate)) as cartDateTimestamp
                  FROM object_$cartClassId AS carts
                  WHERE o_creationDate > $fromTimestamp AND o_creationDate < $toTimestamp
                  GROUP BY DATE(FROM_UNIXTIME(o_creationDate))
                ) as cartsQuery ON cartsQuery.cartDateTimestamp = ordersQuery.orderDateTimestamp
            ";
        }

        $data = $this->db->fetchAll(implode(PHP_EOL . "UNION ALL" . PHP_EOL, $queries) . '  ORDER BY timestamp ASC');

        foreach ($data as &$day) {
            $date = Carbon::createFromTimestamp(strtotime($day['timestamp']));

            $day['datetext'] = $date->toDateString();
        }

        return array_values($data);
    }
}
